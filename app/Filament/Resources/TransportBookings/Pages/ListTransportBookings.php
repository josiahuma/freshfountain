<?php

namespace App\Filament\Resources\TransportBookings\Pages;

use App\Filament\Resources\TransportBookings\TransportBookingResource;
use App\Models\TransportBooking;
use App\Models\TransportPickupEvent;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransportBookings extends ListRecords
{
    protected static string $resource = TransportBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publicBooking')
                ->label('Public booking page')
                ->icon('heroicon-o-globe-alt')
                ->url(route('transport.index'))
                ->openUrlInNewTab(),

            CreateAction::make()
                ->label('Record booking'),
        ];
    }

    public function getSubheading(): ?string
    {
        $event = TransportPickupEvent::query()
            ->upcoming()
            ->orderBy('pickup_date')
            ->first();

        if (! $event) {
            return 'Passenger bookings, pickup times and driver navigation.';
        }

        $bookings = TransportBooking::query()
            ->where('transport_pickup_event_id', $event->id)
            ->where('status', TransportBooking::STATUS_CONFIRMED);

        return sprintf(
            '%s — %d booking(s), %d passenger(s).',
            $event->pickup_date->format('d M Y') . ' ' . $event->title,
            (clone $bookings)->count(),
            (int) (clone $bookings)->sum('party_size'),
        );
    }
}
