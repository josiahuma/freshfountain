<?php

namespace App\Filament\Resources\UnitMembershipRequests;

use App\Filament\Resources\UnitMembershipRequests\Pages\CreateUnitMembershipRequest;
use App\Filament\Resources\UnitMembershipRequests\Pages\EditUnitMembershipRequest;
use App\Filament\Resources\UnitMembershipRequests\Pages\ListUnitMembershipRequests;
use App\Filament\Resources\UnitMembershipRequests\Pages\ViewUnitMembershipRequest;
use App\Filament\Resources\UnitMembershipRequests\Schemas\UnitMembershipRequestForm;
use App\Filament\Resources\UnitMembershipRequests\Schemas\UnitMembershipRequestInfolist;
use App\Filament\Resources\UnitMembershipRequests\Tables\UnitMembershipRequestsTable;
use App\Models\UnitMembershipRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UnitMembershipRequestResource extends Resource
{
    protected static ?string $model =
        UnitMembershipRequest::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup =
        'Church CRM';

    protected static ?string $navigationLabel =
        'Unit Requests';

    protected static ?string $modelLabel =
        'Unit Request';

    protected static ?string $pluralModelLabel =
        'Unit Requests';

    protected static ?string $recordTitleAttribute =
        'display_name';

    protected static ?int $navigationSort = 4;

    public static function form(
        Schema $schema
    ): Schema {
        return UnitMembershipRequestForm::configure(
            $schema
        );
    }

    public static function infolist(
        Schema $schema
    ): Schema {
        return UnitMembershipRequestInfolist::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {
        return UnitMembershipRequestsTable::configure(
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
                ListUnitMembershipRequests::route('/'),

            'create' =>
                CreateUnitMembershipRequest::route('/create'),

            'view' =>
                ViewUnitMembershipRequest::route('/{record}'),

            'edit' =>
                EditUnitMembershipRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = UnitMembershipRequest::query()
            ->pending()
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}