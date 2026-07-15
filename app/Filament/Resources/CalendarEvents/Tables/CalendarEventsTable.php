<?php

namespace App\Filament\Resources\CalendarEvents\Tables;

use App\Models\CalendarEvent;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CalendarEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'asc')
            ->columns([
                ColorColumn::make('colour')
                    ->label('')
                    ->tooltip('Calendar colour'),

                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->visibility('public')
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('starts_at')
                    ->label('Date')
                    ->date('D, d M Y')
                    ->sortable(),

                TextColumn::make('event_time')
                    ->label('Time')
                    ->state(
                        fn (CalendarEvent $record): string =>
                            $record->is_all_day
                                ? 'All day'
                                : $record->time_label
                    )
                    ->badge()
                    ->color(
                        fn (CalendarEvent $record): string =>
                            $record->is_all_day
                                ? 'gray'
                                : 'info'
                    ),

                TextColumn::make('category')
                    ->label('Category')
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            CalendarEvent::categoryOptions()[$state ?? 'general']
                                ?? ucfirst($state ?? 'General')
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'service' => 'info',
                            'prayer' => 'danger',
                            'worship' => 'primary',
                            'conference' => 'primary',
                            'outreach' => 'success',
                            'youth' => 'warning',
                            'children' => 'info',
                            'workers' => 'gray',
                            'community' => 'success',
                            'anniversary' => 'warning',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedStar)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(CalendarEvent::categoryOptions())
                    ->native(false),

                TernaryFilter::make('is_published')
                    ->label('Published status')
                    ->placeholder('All events')
                    ->trueLabel('Published events')
                    ->falseLabel('Unpublished events'),

                TernaryFilter::make('is_featured')
                    ->label('Featured status')
                    ->placeholder('All events')
                    ->trueLabel('Featured events')
                    ->falseLabel('Not featured'),

                Filter::make('upcoming')
                    ->label('Upcoming events')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query->where(
                                function (Builder $query): void {
                                    $query
                                        ->where(
                                            'starts_at',
                                            '>=',
                                            now()->startOfDay()
                                        )
                                        ->orWhere(
                                            'ends_at',
                                            '>=',
                                            now()
                                        );
                                }
                            )
                    ),

                Filter::make('past')
                    ->label('Past events')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query
                                ->where(
                                    'starts_at',
                                    '<',
                                    now()->startOfDay()
                                )
                                ->where(
                                    function (Builder $query): void {
                                        $query
                                            ->whereNull('ends_at')
                                            ->orWhere(
                                                'ends_at',
                                                '<',
                                                now()
                                            );
                                    }
                                )
                    ),

                Filter::make('date_range')
                    ->label('Date range')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From')
                            ->native(false),

                        DatePicker::make('until')
                            ->label('Until')
                            ->native(false),
                    ])
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {
                            return $query
                                ->when(
                                    $data['from'] ?? null,
                                    fn (
                                        Builder $query,
                                        string $date
                                    ): Builder =>
                                        $query->whereDate(
                                            'starts_at',
                                            '>=',
                                            $date
                                        )
                                )
                                ->when(
                                    $data['until'] ?? null,
                                    fn (
                                        Builder $query,
                                        string $date
                                    ): Builder =>
                                        $query->whereDate(
                                            'starts_at',
                                            '<=',
                                            $date
                                        )
                                );
                        }
                    ),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('publish')
                    ->label('Publish')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn (CalendarEvent $record): bool =>
                            ! $record->is_published
                    )
                    ->action(
                        function (CalendarEvent $record): void {
                            $record->update([
                                'is_published' => true,
                            ]);

                            Notification::make()
                                ->title('Event published')
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(
                        fn (CalendarEvent $record): bool =>
                            $record->is_published
                    )
                    ->action(
                        function (CalendarEvent $record): void {
                            $record->update([
                                'is_published' => false,
                            ]);

                            Notification::make()
                                ->title('Event unpublished')
                                ->success()
                                ->send();
                        }
                    ),

                ReplicateAction::make()
                    ->label('Duplicate')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->excludeAttributes([
                        'eventib_event_id',
                    ])
                    ->beforeReplicaSaved(
                        function (CalendarEvent $replica): void {
                            $replica->title =
                                $replica->title . ' – Copy';

                            $replica->source =
                                CalendarEvent::SOURCE_INTERNAL;

                            $replica->eventib_event_id = null;

                            $replica->is_published = false;

                            $replica->is_featured = false;
                        }
                    )
                    ->successNotificationTitle(
                        'Event duplicated as an unpublished copy'
                    ),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publish selected')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(
                            function (Collection $records): void {
                                $records->each(
                                    fn (CalendarEvent $record) =>
                                        $record->update([
                                            'is_published' => true,
                                        ])
                                );

                                Notification::make()
                                    ->title('Selected events published')
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('unpublish')
                        ->label('Unpublish selected')
                        ->icon(Heroicon::OutlinedEyeSlash)
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(
                            function (Collection $records): void {
                                $records->each(
                                    fn (CalendarEvent $record) =>
                                        $record->update([
                                            'is_published' => false,
                                        ])
                                );

                                Notification::make()
                                    ->title('Selected events unpublished')
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No calendar events yet')
            ->emptyStateDescription(
                'Create the first church event to begin building the public calendar.'
            )
            ->emptyStateIcon(Heroicon::OutlinedCalendarDays);
    }
}