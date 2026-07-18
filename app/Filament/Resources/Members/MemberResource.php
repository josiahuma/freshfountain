<?php

namespace App\Filament\Resources\Members;

use App\Filament\Concerns\AuthorizesModuleAccess;

use App\Filament\Resources\Members\Pages\CreateMember;
use App\Filament\Resources\Members\Pages\EditMember;
use App\Filament\Resources\Members\Pages\ListMembers;
use App\Filament\Resources\Members\Pages\ViewMember;
use App\Filament\Resources\Members\Schemas\MemberForm;
use App\Filament\Resources\Members\Schemas\MemberInfolist;
use App\Filament\Resources\Members\Tables\MembersTable;
use App\Models\Member;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MemberResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = Member::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup =
        'Church CRM';

    protected static ?string $navigationLabel =
        'Members';

    protected static ?string $modelLabel =
        'Member';

    protected static ?string $pluralModelLabel =
        'Members';

    protected static ?string $recordTitleAttribute =
        'display_name';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MemberForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MemberInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembers::route('/'),
            'create' => CreateMember::route('/create'),
            'view' => ViewMember::route('/{record}'),
            'edit' => EditMember::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'user',
                'churchUnit',
                'leader',
                'churchUnits',
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Member::query()
            ->where('is_active', true)
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile_number',
            'alternative_phone',
            'churchUnit.name',
            'churchUnits.name',
        ];
    }

    public static function getGlobalSearchResultTitle(
        Model $record
    ): string {
        /** @var Member $record */
        return $record->display_name;
    }

    public static function getGlobalSearchResultDetails(
        Model $record
    ): array {
        /** @var Member $record */

        $record->loadMissing([
            'churchUnit',
            'churchUnits',
        ]);

        $allUnits = $record->churchUnits
            ->pluck('name')
            ->filter()
            ->implode(', ');

        return [
            'Email' =>
                $record->email
                ?: 'Not provided',

            'Mobile' =>
                $record->mobile_number
                ?: 'Not provided',

            'Primary Unit' =>
                $record->churchUnit?->name
                ?: 'Not assigned',

            'All Units' =>
                $allUnits
                ?: 'No unit memberships',

            'Status' =>
                Member::statusOptions()[
                    $record->membership_status
                ] ?? ucfirst(
                    $record->membership_status
                ),
        ];
    }

    protected static function permissionModule(): string
    {
        return 'members';
    }
}