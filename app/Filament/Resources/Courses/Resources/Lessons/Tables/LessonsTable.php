<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Tables;

use App\Models\Lesson;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('title')
                    ->label('Lesson')
                    ->description(
                        fn (Lesson $record): ?string =>
                            $record->summary
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('video_provider')
                    ->label('Video')
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            match ($state) {
                                Lesson::PROVIDER_YOUTUBE =>
                                    'YouTube',

                                Lesson::PROVIDER_VIMEO =>
                                    'Vimeo',

                                default => 'No video',
                            }
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                            match ($state) {
                                Lesson::PROVIDER_YOUTUBE =>
                                    'danger',

                                Lesson::PROVIDER_VIMEO =>
                                    'info',

                                default => 'gray',
                            }
                    ),

                TextColumn::make(
                    'video_duration_minutes'
                )
                    ->label('Duration')
                    ->formatStateUsing(
                        fn (?int $state): string =>
                            $state
                                ? "{$state} min"
                                : 'Not set'
                    )
                    ->toggleable(),

                IconColumn::make('is_preview')
                    ->label('Preview')
                    ->boolean()
                    ->trueIcon(
                        Heroicon::OutlinedEye
                    )
                    ->falseIcon(
                        Heroicon::OutlinedLockClosed
                    )
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                IconColumn::make(
                    'requires_manual_completion'
                )
                    ->label('Completion')
                    ->boolean()
                    ->trueIcon(
                        Heroicon::OutlinedCheckCircle
                    )
                    ->falseIcon(
                        Heroicon::OutlinedMinus
                    )
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                SelectFilter::make('video_provider')
                    ->label('Video provider')
                    ->options(
                        Lesson::videoProviderOptions()
                    )
                    ->native(false),

                TernaryFilter::make('is_published')
                    ->label('Published status')
                    ->placeholder('All lessons')
                    ->trueLabel('Published lessons')
                    ->falseLabel('Unpublished lessons'),

                TernaryFilter::make('is_preview')
                    ->label('Preview status')
                    ->placeholder('All lessons')
                    ->trueLabel('Preview lessons')
                    ->falseLabel('Enrolled students only'),
            ])
            ->recordActions([
                EditAction::make(),

                ReplicateAction::make()
                    ->label('Duplicate')
                    ->icon(
                        Heroicon::OutlinedDocumentDuplicate
                    )
                    ->excludeAttributes([
                        'slug',
                    ])
                    ->beforeReplicaSaved(
                        function (Lesson $replica): void {
                            $replica->title =
                                $replica->title . ' – Copy';

                            $replica->slug = null;

                            $replica->is_published = false;

                            $replica->is_preview = false;
                        }
                    )
                    ->successNotificationTitle(
                        'Lesson duplicated as an unpublished copy'
                    ),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No lessons yet')
            ->emptyStateDescription(
                'Add the first lesson to this course.'
            )
            ->emptyStateIcon(
                Heroicon::OutlinedPlayCircle
            );
    }
}