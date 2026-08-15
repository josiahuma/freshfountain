<?php

namespace App\Filament\Resources\TransportBookings;

use App\Filament\Concerns\AuthorizesModuleAccess;
use App\Filament\Resources\TransportBookings\Pages\CreateTransportBooking;
use App\Filament\Resources\TransportBookings\Pages\EditTransportBooking;
use App\Filament\Resources\TransportBookings\Pages\ListTransportBookings;
use App\Filament\Resources\TransportBookings\Schemas\TransportBookingForm;
use App\Filament\Resources\TransportBookings\Tables\TransportBookingsTable;
use App\Models\TransportBooking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransportBookingResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = TransportBooking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Transport';

    protected static ?string $navigationLabel = 'Bookings';

    protected static ?string $modelLabel = 'Transport Booking';

    protected static ?string $pluralModelLabel = 'Transport Bookings';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TransportBookingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransportBookingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransportBookings::route('/'),
            'create' => CreateTransportBooking::route('/create'),
            'edit' => EditTransportBooking::route('/{record}/edit'),
        ];
    }

    protected static function permissionModule(): string
    {
        return 'transport';
    }
}
