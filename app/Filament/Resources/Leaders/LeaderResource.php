<?php

namespace App\Filament\Resources\Leaders;

use App\Filament\Concerns\AuthorizesModuleAccess;

use App\Filament\Resources\Leaders\Pages\CreateLeader;
use App\Filament\Resources\Leaders\Pages\EditLeader;
use App\Filament\Resources\Leaders\Pages\ListLeaders;
use App\Filament\Resources\Leaders\Pages\ViewLeader;
use App\Filament\Resources\Leaders\Schemas\LeaderForm;
use App\Filament\Resources\Leaders\Schemas\LeaderInfolist;
use App\Filament\Resources\Leaders\Tables\LeadersTable;
use App\Models\Leader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LeaderResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = Leader::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup =
        'Church CRM';

    protected static ?string $navigationLabel =
        'Leaders';

    protected static ?string $modelLabel =
        'Leader';

    protected static ?string $pluralModelLabel =
        'Leaders';

    protected static ?string $recordTitleAttribute =
        'display_name';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return LeaderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaders::route('/'),
            'create' => CreateLeader::route('/create'),
            'view' => ViewLeader::route('/{record}'),
            'edit' => EditLeader::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Leader::query()
            ->where('is_active', true)
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile_number',
            'leadership_role',
            'churchUnit.name',
        ];
    }

    public static function getGlobalSearchResultTitle(
        \Illuminate\Database\Eloquent\Model $record
    ): string {
        /** @var Leader $record */
        return $record->display_name;
    }

    public static function getGlobalSearchResultDetails(
        \Illuminate\Database\Eloquent\Model $record
    ): array {
        /** @var Leader $record */
        return [
            'Role' => $record->leadership_role,
            'Unit' => $record->churchUnit?->name
                ?: 'Not assigned',
            'Email' => $record->email
                ?: 'Not provided',
        ];
    }

    protected static function permissionModule(): string
    {
        return 'leaders';
    }
}