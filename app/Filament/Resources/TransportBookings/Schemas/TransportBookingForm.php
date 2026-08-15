<?php

namespace App\Filament\Resources\TransportBookings\Schemas;

use App\Models\TransportBooking;
use App\Models\TransportPickupEvent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking')
                ->description('Passenger details are stored on the transport booking and are not linked to the church member CRM.')
                ->columns(2)
                ->schema([
                    Select::make('transport_pickup_event_id')
                        ->label('Pickup event')
                        ->options(fn (): array => TransportPickupEvent::query()
                            ->orderByDesc('pickup_date')
                            ->get()
                            ->mapWithKeys(fn (TransportPickupEvent $event): array => [
                                $event->id => $event->pickup_date->format('d M Y') . ' — ' . $event->title,
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('name')
                        ->label('Passenger name')
                        ->required()
                        ->maxLength(191),

                    TextInput::make('phone')
                        ->label('Phone number')
                        ->tel()
                        ->required()
                        ->maxLength(191),

                    Textarea::make('address')
                        ->label('Pickup address')
                        ->required()
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    TimePicker::make('pickup_time')
                        ->label('Pickup time')
                        ->seconds(false)
                        ->required(),

                    TextInput::make('party_size')
                        ->label('Number of passengers')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(50)
                        ->default(1)
                        ->required(),

                    Select::make('status')
                        ->options(TransportBooking::statusOptions())
                        ->default(TransportBooking::STATUS_CONFIRMED)
                        ->required(),

                    Textarea::make('notes')
                        ->rows(3)
                        ->maxLength(3000)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
