<?php

namespace App\Filament\Resources\Leaders\Tables;

use App\Models\ChurchUnit;
use App\Models\Leader;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class LeadersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('first_name')
            ->columns([
                TextColumn::make('display_name')
                    ->label('Leader')
                    ->state(
                        fn (Leader $record): string =>
                            $record->display_name
                    )
                    ->description(
                        fn (Leader $record): ?string =>
                            $record->email
                            ?: $record->mobile_number
                    )
                    ->searchable([
                        'first_name',
                        'middle_name',
                        'last_name',
                        'email',
                        'mobile_number',
                    ])
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('leadership_role')
                    ->label('Role')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('churchUnit.name')
                    ->label('Church Unit')
                    ->placeholder('Not assigned')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('assigned_members_count')
                    ->label('Assigned Members')
                    ->counts('assignedMembers')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('mobile_number')
                    ->label('Mobile')
                    ->placeholder('Not provided')
                    ->copyable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->placeholder('Not provided')
                    ->copyable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('member.display_name')
                    ->label('Linked Member')
                    ->state(
                        fn (Leader $record): ?string =>
                            $record->member?->display_name
                    )
                    ->placeholder('Not linked')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('started_at')
                    ->label('Started')
                    ->date('d M Y')
                    ->placeholder('Not provided')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->dateTimeTooltip('d M Y, H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                SelectFilter::make('church_unit_id')
                    ->label('Church Unit')
                    ->options(
                        fn (): array =>
                            ChurchUnit::query()
                                ->ordered()
                                ->pluck('name', 'id')
                                ->all()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('leadership_role')
                    ->label('Role')
                    ->options(
                        fn (): array =>
                            Leader::query()
                                ->whereNotNull(
                                    'leadership_role'
                                )
                                ->distinct()
                                ->orderBy(
                                    'leadership_role'
                                )
                                ->pluck(
                                    'leadership_role',
                                    'leadership_role'
                                )
                                ->all()
                    )
                    ->searchable()
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Active Status'),

                TernaryFilter::make('member_id')
                    ->label('Linked to Member')
                    ->nullable(),
            ])
            ->recordActions([
                ViewAction::make(),

                EditAction::make(),

                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon(
                        Heroicon::OutlinedArchiveBox
                    )
                    ->color('warning')
                    ->visible(
                        fn (Leader $record): bool =>
                            $record->is_active
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Deactivate this leader?'
                    )
                    ->modalDescription(
                        'The leadership record will remain available but will no longer appear as active.'
                    )
                    ->action(
                        function (Leader $record): void {
                            $record->update([
                                'is_active' => false,
                                'ended_at' =>
                                    $record->ended_at
                                    ?? now()->toDateString(),
                            ]);

                            Notification::make()
                                ->title(
                                    'Leader deactivated'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon(
                        Heroicon::OutlinedArrowPath
                    )
                    ->color('success')
                    ->visible(
                        fn (Leader $record): bool =>
                            ! $record->is_active
                    )
                    ->requiresConfirmation()
                    ->action(
                        function (Leader $record): void {
                            $record->update([
                                'is_active' => true,
                                'ended_at' => null,
                            ]);

                            Notification::make()
                                ->title(
                                    'Leader reactivated'
                                )
                                ->success()
                                ->send();
                        }
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('deactivate')
                        ->label(
                            'Deactivate Selected'
                        )
                        ->icon(
                            Heroicon::OutlinedArchiveBox
                        )
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(
                            function (
                                Collection $records
                            ): void {
                                Leader::query()
                                    ->whereKey(
                                        $records->modelKeys()
                                    )
                                    ->update([
                                        'is_active' => false,
                                        'ended_at' =>
                                            now()->toDateString(),
                                    ]);

                                Notification::make()
                                    ->title(
                                        'Selected leaders deactivated'
                                    )
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('reactivate')
                        ->label(
                            'Reactivate Selected'
                        )
                        ->icon(
                            Heroicon::OutlinedArrowPath
                        )
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(
                            function (
                                Collection $records
                            ): void {
                                Leader::query()
                                    ->whereKey(
                                        $records->modelKeys()
                                    )
                                    ->update([
                                        'is_active' => true,
                                        'ended_at' => null,
                                    ]);

                                Notification::make()
                                    ->title(
                                        'Selected leaders reactivated'
                                    )
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading(
                'No leaders yet'
            )
            ->emptyStateDescription(
                'Create leadership records manually or import them from OviBase.'
            )
            ->emptyStateIcon(
                Heroicon::OutlinedUserCircle
            );
    }
}