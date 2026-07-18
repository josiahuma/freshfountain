<?php

namespace App\Filament\Resources\CalendarEvents;

use App\Filament\Concerns\AuthorizesModuleAccess;

use App\Filament\Resources\CalendarEvents\Pages\CreateCalendarEvent;
use App\Filament\Resources\CalendarEvents\Pages\EditCalendarEvent;
use App\Filament\Resources\CalendarEvents\Pages\ListCalendarEvents;
use App\Filament\Resources\CalendarEvents\Schemas\CalendarEventForm;
use App\Filament\Resources\CalendarEvents\Tables\CalendarEventsTable;
use App\Models\CalendarEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CalendarEventResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = CalendarEvent::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup =
        'Church Calendar';

    protected static ?string $navigationLabel = 'Calendar Events';

    protected static ?string $modelLabel = 'Calendar Event';

    protected static ?string $pluralModelLabel = 'Calendar Events';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CalendarEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalendarEventsTable::configure($table);
    }

    /**
     * Only show events created and managed by this website.
     *
     * Eventib events will be fetched externally later and should not appear
     * as editable records in this resource.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('source', CalendarEvent::SOURCE_INTERNAL);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalendarEvents::route('/'),
            'create' => CreateCalendarEvent::route('/create'),
            'edit' => EditCalendarEvent::route('/{record}/edit'),
        ];
    }

    /**
     * Show the number of published upcoming events beside the navigation item.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = CalendarEvent::query()
            ->internal()
            ->published()
            ->upcoming()
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    protected static function permissionModule(): string
    {
        return 'calendar';
    }
}