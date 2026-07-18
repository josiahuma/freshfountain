<?php

namespace App\Filament\Resources\IncomeCategories;

use App\Filament\Concerns\AuthorizesModuleAccess;

use App\Filament\Resources\IncomeCategories\Pages\CreateIncomeCategory;
use App\Filament\Resources\IncomeCategories\Pages\EditIncomeCategory;
use App\Filament\Resources\IncomeCategories\Pages\ListIncomeCategories;
use App\Filament\Resources\IncomeCategories\Schemas\IncomeCategoryForm;
use App\Filament\Resources\IncomeCategories\Tables\IncomeCategoriesTable;
use App\Models\IncomeCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IncomeCategoryResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = IncomeCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Income Categories';

    protected static ?int $navigationSort = 31;

    protected static ?string $modelLabel = 'Income Category';

    protected static ?string $pluralModelLabel = 'Income Categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return IncomeCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomeCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncomeCategories::route('/'),
            'create' => CreateIncomeCategory::route('/create'),
            'edit' => EditIncomeCategory::route('/{record}/edit'),
        ];
    }

    protected static function permissionModule(): string
    {
        return 'finance';
    }
}