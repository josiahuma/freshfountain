<?php

namespace App\Filament\Resources\SmsLogs;

use App\Filament\Resources\SmsLogs\Pages\ListSmsLogs;
use App\Filament\Resources\SmsLogs\Tables\SmsLogsTable;
use App\Models\SmsLog;
use App\Support\Access\BackendAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SmsLogResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Church CRM';

    protected static ?string $navigationLabel = 'SMS History';

    protected static ?string $modelLabel = 'SMS history';

    protected static ?string $pluralModelLabel = 'SMS history';

    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return SmsLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsLogs::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return BackendAccess::canView('members');
    }

    public static function canViewAny(): bool
    {
        return BackendAccess::canView('members');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
