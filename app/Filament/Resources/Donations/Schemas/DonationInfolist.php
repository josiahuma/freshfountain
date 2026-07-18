<?php

namespace App\Filament\Resources\Donations\Schemas;

use App\Support\Privacy\DonorPrivacy;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DonationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('donationFund.name')
                    ->label('Donation fund')
                    ->placeholder('-'),
                TextEntry::make('member_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('finance_transaction_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('currency'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                IconEntry::make('is_recurring')
                    ->boolean(),
                TextEntry::make('recurring_interval')
                    ->placeholder('-'),
                IconEntry::make('gift_aid')
                    ->boolean(),
                IconEntry::make('is_anonymous')
                    ->boolean(),
                TextEntry::make('recorded_by_user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('donor_name')
                    ->formatStateUsing(fn (?string $state): string => DonorPrivacy::name($state))
                    ->placeholder('-'),
                TextEntry::make('donor_email')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::email($state))
                    ->placeholder('-'),
                TextEntry::make('donor_phone')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::phone($state))
                    ->placeholder('-'),
                TextEntry::make('address_line_1')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::text($state))
                    ->placeholder('-'),
                TextEntry::make('address_line_2')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::text($state))
                    ->placeholder('-'),
                TextEntry::make('city')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::text($state))
                    ->placeholder('-'),
                TextEntry::make('county')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::text($state))
                    ->placeholder('-'),
                TextEntry::make('postcode')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::text($state))
                    ->placeholder('-'),
                TextEntry::make('country')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::text($state))
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('payment_provider'),
                TextEntry::make('stripe_session_id')
                    ->placeholder('-'),
                TextEntry::make('stripe_payment_intent_id')
                    ->placeholder('-'),
                TextEntry::make('stripe_subscription_id')
                    ->placeholder('-'),
                TextEntry::make('stripe_customer_id')
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('failed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('cancelled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('failure_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('legacy_ovibase_id')
                    ->placeholder('-'),
                TextEntry::make('legacy_tenant_id')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
