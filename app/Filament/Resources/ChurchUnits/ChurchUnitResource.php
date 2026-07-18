<?php

namespace App\Filament\Resources\ChurchUnits;

use App\Filament\Concerns\AuthorizesModuleAccess;

use App\Filament\Resources\ChurchUnits\Pages\CreateChurchUnit;
use App\Filament\Resources\ChurchUnits\Pages\EditChurchUnit;
use App\Filament\Resources\ChurchUnits\Pages\ListChurchUnits;
use App\Filament\Resources\ChurchUnits\Pages\ViewChurchUnit;
use App\Filament\Resources\ChurchUnits\Schemas\ChurchUnitForm;
use App\Filament\Resources\ChurchUnits\Schemas\ChurchUnitInfolist;
use App\Filament\Resources\ChurchUnits\Tables\ChurchUnitsTable;
use App\Models\ChurchUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ChurchUnitResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model =
        ChurchUnit::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup =
        'Church CRM';

    protected static ?string $navigationLabel =
        'Church Units';

    protected static ?string $modelLabel =
        'Church Unit';

    protected static ?string $pluralModelLabel =
        'Church Units';

    protected static ?string $recordTitleAttribute =
        'name';

    protected static ?int $navigationSort = 1;

    public static function form(
        Schema $schema
    ): Schema {
        return ChurchUnitForm::configure(
            $schema
        );
    }

    public static function infolist(
        Schema $schema
    ): Schema {
        return ChurchUnitInfolist::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {
        return ChurchUnitsTable::configure(
            $table
        );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' =>
                ListChurchUnits::route('/'),

            'create' =>
                CreateChurchUnit::route('/create'),

            'view' =>
                ViewChurchUnit::route('/{record}'),

            'edit' =>
                EditChurchUnit::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ChurchUnit::query()
            ->active()
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'alias',
            'description',
        ];
    }

    protected static function permissionModule(): string
    {
        return 'church_units';
    }
}