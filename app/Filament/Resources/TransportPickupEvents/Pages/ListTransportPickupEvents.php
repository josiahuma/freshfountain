<?php

namespace App\Filament\Resources\TransportPickupEvents\Pages;

use App\Filament\Resources\TransportPickupEvents\TransportPickupEventResource;
use App\Models\TransportPickupEvent;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransportPickupEvents extends ListRecords
{
    protected static string $resource = TransportPickupEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publicBooking')
                ->label('Public booking page')
                ->icon('heroicon-o-globe-alt')
                ->url(route('transport.index'))
                ->openUrlInNewTab(),

            CreateAction::make()
                ->label('Open pickup event'),
        ];
    }

    public function getSubheading(): ?string
    {
        $next = TransportPickupEvent::query()
            ->upcoming()
            ->orderBy('pickup_date')
            ->first();

        if (! $next) {
            return 'Create pickup dates and manage when passenger bookings are open.';
        }

        return sprintf(
            'Next pickup: %s on %s. %d booking(s), %d passenger(s).',
            $next->title,
            $next->pickup_date->format('d M Y'),
            $next->booking_count,
            $next->booked_seats,
        );
    }
}
