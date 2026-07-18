<?php

namespace App\Filament\Resources\FinanceTransactions;

use App\Filament\Concerns\AuthorizesModuleAccess;

use App\Filament\Resources\FinanceTransactions\Pages\CreateFinanceTransaction;
use App\Filament\Resources\FinanceTransactions\Pages\EditFinanceTransaction;
use App\Filament\Resources\FinanceTransactions\Pages\ListFinanceTransactions;
use App\Filament\Resources\FinanceTransactions\Schemas\FinanceTransactionForm;
use App\Filament\Resources\FinanceTransactions\Tables\FinanceTransactionsTable;
use App\Models\FinanceTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FinanceTransactionResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = FinanceTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Transactions';

    protected static ?int $navigationSort = 33;

    protected static ?string $modelLabel = 'Finance Transaction';

    protected static ?string $pluralModelLabel = 'Finance Transactions';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function form(Schema $schema): Schema
    {
        return FinanceTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceTransactions::route('/'),
            'create' => CreateFinanceTransaction::route('/create'),
            'edit' => EditFinanceTransaction::route('/{record}/edit'),
        ];
    }

    protected static function permissionModule(): string
    {
        return 'finance';
    }
}