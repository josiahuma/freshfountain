<?php

namespace App\Filament\Resources\SmsTemplates;

use App\Filament\Resources\SmsTemplates\Pages\CreateSmsTemplate;
use App\Filament\Resources\SmsTemplates\Pages\EditSmsTemplate;
use App\Filament\Resources\SmsTemplates\Pages\ListSmsTemplates;
use App\Filament\Resources\SmsTemplates\Schemas\SmsTemplateForm;
use App\Filament\Resources\SmsTemplates\Tables\SmsTemplatesTable;
use App\Models\SmsTemplate;
use App\Support\Access\BackendAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SmsTemplateResource extends Resource
{
    protected static ?string $model = SmsTemplate::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static string|UnitEnum|null $navigationGroup = 'Church CRM';
    protected static ?string $navigationLabel = 'SMS Templates';
    protected static ?string $modelLabel = 'SMS template';
    protected static ?string $pluralModelLabel = 'SMS templates';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema { return SmsTemplateForm::configure($schema); }
    public static function table(Table $table): Table { return SmsTemplatesTable::configure($table); }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsTemplates::route('/'),
            'create' => CreateSmsTemplate::route('/create'),
            'edit' => EditSmsTemplate::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool { return BackendAccess::canView('members'); }
    public static function canViewAny(): bool { return BackendAccess::canView('members'); }
    public static function canCreate(): bool { return BackendAccess::canManage('members'); }
    public static function canEdit(Model $record): bool { return BackendAccess::canManage('members'); }
    public static function canDelete(Model $record): bool { return BackendAccess::canManage('members'); }
    public static function canDeleteAny(): bool { return BackendAccess::canManage('members'); }
}
