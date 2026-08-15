<?php

namespace App\Filament\Resources\TransportPickupEvents;

use App\Filament\Concerns\AuthorizesModuleAccess;
use App\Filament\Resources\TransportPickupEvents\Pages\CreateTransportPickupEvent;
use App\Filament\Resources\TransportPickupEvents\Pages\EditTransportPickupEvent;
use App\Filament\Resources\TransportPickupEvents\Pages\ListTransportPickupEvents;
use App\Filament\Resources\TransportPickupEvents\Schemas\TransportPickupEventForm;
use App\Filament\Resources\TransportPickupEvents\Tables\TransportPickupEventsTable;
use App\Models\TransportPickupEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransportPickupEventResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = TransportPickupEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Transport';

    protected static ?string $navigationLabel = 'Pickup Events';

    protected static ?string $modelLabel = 'Pickup Event';

    protected static ?string $pluralModelLabel = 'Pickup Events';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return TransportPickupEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransportPickupEventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransportPickupEvents::route('/'),
            'create' => CreateTransportPickupEvent::route('/create'),
            'edit' => EditTransportPickupEvent::route('/{record}/edit'),
        ];
    }

    protected static function permissionModule(): string
    {
        return 'transport';
    }
}
