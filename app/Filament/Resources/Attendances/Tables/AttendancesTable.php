<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('service_date', 'desc')
            ->columns([
                TextColumn::make('service_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('service_name')
                    ->label('Service / Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('men')->numeric()->sortable(),
                TextColumn::make('women')->numeric()->sortable(),
                TextColumn::make('children')->numeric()->sortable(),
                TextColumn::make('visitors')->numeric()->sortable(),
                TextColumn::make('online')->numeric()->sortable()->toggleable(),

                TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('creator.name')
                    ->label('Recorded by')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('legacy_source')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'ovibase' ? 'OviBase' : 'Fresh Fountain')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service_name')
                    ->label('Service / Event')
                    ->options(fn (): array => \App\Models\Attendance::query()
                        ->whereNotNull('service_name')
                        ->distinct()
                        ->orderBy('service_name')
                        ->pluck('service_name', 'service_name')
                        ->all()),

                SelectFilter::make('year')
                    ->options(fn (): array => \App\Models\Attendance::query()
                        ->selectRaw('YEAR(service_date) as year')
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->mapWithKeys(fn ($year) => [(string) $year => (string) $year])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder =>
                        filled($data['value'] ?? null)
                            ? $query->whereYear('service_date', (int) $data['value'])
                            : $query),

                Filter::make('this_month')
                    ->label('This month')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereYear('service_date', now()->year)
                        ->whereMonth('service_date', now()->month)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
