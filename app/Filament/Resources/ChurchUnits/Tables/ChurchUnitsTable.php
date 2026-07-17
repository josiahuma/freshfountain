<?php

namespace App\Filament\Resources\ChurchUnits\Tables;

use App\Models\ChurchUnit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ChurchUnitsTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->defaultSort(
                'sort_order'
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Unit')
                    ->description(
                        fn (
                            ChurchUnit $record
                        ): ?string =>
                            $record->alias
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('leaders_count')
                    ->label('Leaders')
                    ->counts('leaders')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('meeting_day')
                    ->label('Meeting day')
                    ->placeholder('Not set')
                    ->toggleable(),

                TextColumn::make('meeting_time')
                    ->label('Meeting time')
                    ->placeholder('Not set')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->dateTimeTooltip(
                        'd M Y, H:i'
                    )
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active status'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(
                'No church units yet'
            )
            ->emptyStateDescription(
                'Create ministry and operational units such as Choir, Media, Children and Follow Up.'
            )
            ->emptyStateIcon(
                Heroicon::OutlinedUserGroup
            );
    }
}