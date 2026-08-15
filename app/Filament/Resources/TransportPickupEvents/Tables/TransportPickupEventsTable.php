<?php

namespace App\Filament\Resources\TransportPickupEvents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransportPickupEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('pickup_date', 'desc')
            ->columns([
                TextColumn::make('pickup_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Pickup event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('pickup_start_time')
                    ->label('Starts')
                    ->time('g:i A'),

                TextColumn::make('pickup_end_time')
                    ->label('Ends')
                    ->time('g:i A'),

                TextColumn::make('interval_minutes')
                    ->label('Interval')
                    ->suffix(' min')
                    ->toggleable(),

                TextColumn::make('capacity_per_slot')
                    ->label('Slot capacity')
                    ->numeric(),

                TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->counts('bookings')
                    ->badge(),

                TextColumn::make('booked_seats')
                    ->label('Passengers')
                    ->state(fn ($record): int => $record->booked_seats)
                    ->numeric()
                    ->weight('bold'),

                IconColumn::make('bookings_open')
                    ->label('Open')
                    ->boolean(),

                TextColumn::make('legacy_source')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'ovipoint' ? 'Ovipoint' : 'Fresh Fountain')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Upcoming')
                    ->query(fn (Builder $query): Builder => $query->whereDate('pickup_date', '>=', today())),

                Filter::make('open')
                    ->label('Bookings open')
                    ->query(fn (Builder $query): Builder => $query->where('bookings_open', true)),
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
