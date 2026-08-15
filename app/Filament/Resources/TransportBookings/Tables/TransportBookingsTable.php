<?php

namespace App\Filament\Resources\TransportBookings\Tables;

use App\Models\TransportBooking;
use App\Models\TransportPickupEvent;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransportBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('pickupEvent.pickup_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('pickupEvent.title')
                    ->label('Event')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('pickup_time')
                    ->label('Pickup')
                    ->time('g:i A')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Passenger')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('party_size')
                    ->label('People')
                    ->numeric()
                    ->badge(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('address')
                    ->label('Pickup address')
                    ->searchable()
                    ->wrap()
                    ->limit(55),

                TextColumn::make('notifications')
                    ->label('Alerts')
                    ->state(function (TransportBooking $record): string {
                        $channels = $record->notificationLogs()
                            ->where('status', 'sent')
                            ->pluck('channel')
                            ->unique()
                            ->map(fn (string $channel): string => strtoupper($channel))
                            ->values();

                        return $channels->isEmpty()
                            ? '—'
                            : $channels->join(' + ');
                    })
                    ->badge()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => TransportBooking::statusOptions()[$state] ?? ucfirst((string) $state)),

                TextColumn::make('legacy_source')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'ovipoint' ? 'Ovipoint' : 'Fresh Fountain')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('transport_pickup_event_id')
                    ->label('Pickup event')
                    ->options(fn (): array => TransportPickupEvent::query()
                        ->orderByDesc('pickup_date')
                        ->get()
                        ->mapWithKeys(fn (TransportPickupEvent $event): array => [
                            $event->id => $event->pickup_date->format('d M Y') . ' — ' . $event->title,
                        ])
                        ->all()),

                SelectFilter::make('status')
                    ->options(TransportBooking::statusOptions()),
            ])
            ->recordActions([
                Action::make('googleMaps')
                    ->label('Google')
                    ->icon('heroicon-o-map')
                    ->url(fn (TransportBooking $record): string => $record->google_maps_url)
                    ->openUrlInNewTab(),

                Action::make('waze')
                    ->label('Waze')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (TransportBooking $record): string => $record->waze_url)
                    ->openUrlInNewTab(),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
