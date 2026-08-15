<?php

namespace App\Filament\Resources\TransportPickupEvents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportPickupEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pickup event')
                ->description('Create the date and pickup window passengers will be able to book.')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Event title')
                        ->placeholder('Sunday Encounter Pickup')
                        ->required()
                        ->maxLength(191)
                        ->columnSpanFull(),

                    DatePicker::make('pickup_date')
                        ->label('Pickup date')
                        ->native(false)
                        ->default(now())
                        ->required(),

                    Toggle::make('bookings_open')
                        ->label('Bookings open')
                        ->default(true)
                        ->helperText('Turn this off to stop new public bookings.'),
                ]),

            Section::make('Pickup slots')
                ->description('Capacity is per time slot, matching the original Ovipoint behaviour.')
                ->columns(4)
                ->schema([
                    TimePicker::make('pickup_start_time')
                        ->label('Start time')
                        ->seconds(false)
                        ->default('09:30')
                        ->required(),

                    TimePicker::make('pickup_end_time')
                        ->label('End time')
                        ->seconds(false)
                        ->default('10:45')
                        ->required(),

                    TextInput::make('interval_minutes')
                        ->label('Interval (minutes)')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(180)
                        ->default(15)
                        ->required(),

                    TextInput::make('capacity_per_slot')
                        ->label('Capacity per slot')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(1000)
                        ->default(10)
                        ->required(),
                ]),

            Section::make('Notes')
                ->collapsed()
                ->schema([
                    Textarea::make('notes')
                        ->rows(4)
                        ->maxLength(3000)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
