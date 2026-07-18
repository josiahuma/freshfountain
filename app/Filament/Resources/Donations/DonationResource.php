<?php

namespace App\Filament\Resources\Donations;

use App\Filament\Concerns\AuthorizesModuleAccess;

use App\Filament\Resources\Donations\Pages\CreateDonation;
use App\Filament\Resources\Donations\Pages\EditDonation;
use App\Filament\Resources\Donations\Pages\ListDonations;
use App\Filament\Resources\Donations\Pages\ViewDonation;
use App\Filament\Resources\Donations\Schemas\DonationForm;
use App\Filament\Resources\Donations\Schemas\DonationInfolist;
use App\Filament\Resources\Donations\Tables\DonationsTable;
use App\Models\Donation;
use App\Support\Privacy\DonorPrivacy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class DonationResource extends Resource
{
    use AuthorizesModuleAccess;

    protected static ?string $model = Donation::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Donations';

    protected static ?string $modelLabel = 'Donation';

    protected static ?string $pluralModelLabel = 'Donations';

    protected static ?string $recordTitleAttribute = 'donor_name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return DonationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationsTable::configure($table);
    }

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record instanceof Donation) {
            return 'Donation';
        }

        if ($record->is_anonymous) {
            return 'Anonymous Donation';
        }

        if (filled($record->donor_name)) {
            return DonorPrivacy::name($record->donor_name);
        }

        if ($record->member) {
            $memberName = collect([
                $record->member->first_name ?? null,
                $record->member->middle_name ?? null,
                $record->member->last_name ?? null,
            ])
                ->filter()
                ->implode(' ');

            if (filled($memberName)) {
                return DonorPrivacy::name($memberName);
            }

            if (filled($record->member->name ?? null)) {
                return DonorPrivacy::name($record->member->name);
            }
        }

        return 'Donation #'.$record->getKey();
    }

    public static function getGloballySearchableAttributes(): array
    {
        $attributes = [
            'stripe_session_id',
            'stripe_payment_intent_id',
            'legacy_ovibase_id',
            'donationFund.name',
        ];

        if (DonorPrivacy::canViewIdentity()) {
            array_push(
                $attributes,
                'donor_name',
                'donor_email',
                'donor_phone'
            );
        }

        return $attributes;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonations::route('/'),
            'create' => CreateDonation::route('/create'),
            'view' => ViewDonation::route('/{record}'),
            'edit' => EditDonation::route('/{record}/edit'),
        ];
    }

    protected static function permissionModule(): string
    {
        return 'finance';
    }
}