<?php

namespace App\Filament\Resources\AttendanceServiceTypes;

use App\Filament\Concerns\AuthorizesModuleAccess;
use App\Filament\Resources\AttendanceServiceTypes\Pages\CreateAttendanceServiceType;
use App\Filament\Resources\AttendanceServiceTypes\Pages\EditAttendanceServiceType;
use App\Filament\Resources\AttendanceServiceTypes\Pages\ListAttendanceServiceTypes;
use App\Filament\Resources\AttendanceServiceTypes\Schemas\AttendanceServiceTypeForm;
use App\Filament\Resources\AttendanceServiceTypes\Tables\AttendanceServiceTypesTable;
use App\Models\AttendanceServiceType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AttendanceServiceTypeResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = AttendanceServiceType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Church Management';

    protected static ?string $navigationLabel = 'Attendance Service Types';

    protected static ?string $modelLabel = 'attendance service type';

    protected static ?string $pluralModelLabel = 'attendance service types';

    protected static ?int $navigationSort = 26;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AttendanceServiceTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceServiceTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceServiceTypes::route('/'),
            'create' => CreateAttendanceServiceType::route('/create'),
            'edit' => EditAttendanceServiceType::route('/{record}/edit'),
        ];
    }

    protected static function permissionModule(): string
    {
        return 'attendance';
    }
}
