<?php

namespace App\Filament\Resources\DonationFunds;

use App\Filament\Resources\DonationFunds\Pages\CreateDonationFund;
use App\Filament\Resources\DonationFunds\Pages\EditDonationFund;
use App\Filament\Resources\DonationFunds\Pages\ListDonationFunds;
use App\Filament\Resources\DonationFunds\Schemas\DonationFundForm;
use App\Filament\Resources\DonationFunds\Tables\DonationFundsTable;
use App\Models\DonationFund;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DonationFundResource extends Resource
{
    protected static ?string $model = DonationFund::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Donation Funds';

    protected static ?int $navigationSort = 30;

    protected static ?string $modelLabel = 'Donation Fund';

    protected static ?string $pluralModelLabel = 'Donation Funds';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DonationFundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationFundsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonationFunds::route('/'),
            'create' => CreateDonationFund::route('/create'),
            'edit' => EditDonationFund::route('/{record}/edit'),
        ];
    }
}