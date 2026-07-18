<?php

namespace App\Filament\Resources\Donations\Schemas;

use App\Models\Member;
use App\Support\Finance\Money;
use App\Support\Finance\PaymentMethods;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('recorded_by_user_id')
                    ->default(fn (): ?int => auth()->id()),

                Hidden::make('finance_transaction_id'),

                Section::make('Donation Details')
                    ->description(
                        'Record the amount, fund, payment method and current payment status.'
                    )
                    ->columns(2)
                    ->schema([
                        Select::make('donation_fund_id')
                            ->label('Donation Fund')
                            ->relationship(
                                name: 'donationFund',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->prefix('£')
                            ->numeric()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->required(),

                        Select::make('currency')
                            ->label('Currency')
                            ->options(Money::currencies())
                            ->default(Money::DEFAULT_CURRENCY)
                            ->required()
                            ->native(false),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(PaymentMethods::options())
                            ->default(PaymentMethods::CASH)
                            ->searchable()
                            ->native(false)
                            ->required(),

                        Select::make('status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->default('paid')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(
                                function (Set $set, ?string $state): void {
                                    if ($state === 'paid') {
                                        $set('paid_at', now());
                                        $set('failed_at', null);
                                        $set('cancelled_at', null);
                                        $set('failure_reason', null);

                                        return;
                                    }

                                    if ($state === 'failed') {
                                        $set('failed_at', now());
                                        $set('paid_at', null);
                                        $set('cancelled_at', null);

                                        return;
                                    }

                                    if ($state === 'cancelled') {
                                        $set('cancelled_at', now());
                                        $set('paid_at', null);
                                        $set('failed_at', null);

                                        return;
                                    }

                                    if ($state === 'pending') {
                                        $set('paid_at', null);
                                        $set('failed_at', null);
                                        $set('cancelled_at', null);
                                        $set('failure_reason', null);
                                    }
                                }
                            ),

                        Select::make('payment_provider')
                            ->label('Payment Source')
                            ->options([
                                'manual' => 'Manual Entry',
                                'stripe' => 'Stripe',
                                'bank' => 'Bank',
                                'cash' => 'Cash',
                                'cheque' => 'Cheque',
                                'ovibase' => 'OviBase Import',
                                'other' => 'Other',
                            ])
                            ->default('manual')
                            ->required()
                            ->native(false),

                        DateTimePicker::make('paid_at')
                            ->label('Paid At')
                            ->seconds(false)
                            ->native(false)
                            ->visible(
                                fn (Get $get): bool => $get('status') === 'paid'
                            ),

                        DateTimePicker::make('failed_at')
                            ->label('Failed At')
                            ->seconds(false)
                            ->native(false)
                            ->visible(
                                fn (Get $get): bool => $get('status') === 'failed'
                            ),

                        DateTimePicker::make('cancelled_at')
                            ->label('Cancelled At')
                            ->seconds(false)
                            ->native(false)
                            ->visible(
                                fn (Get $get): bool => $get('status') === 'cancelled'
                            ),

                        Textarea::make('failure_reason')
                            ->label('Failure Reason')
                            ->rows(3)
                            ->maxLength(2000)
                            ->visible(
                                fn (Get $get): bool => $get('status') === 'failed'
                            )
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->placeholder(
                                'Add any information useful for finance administration or audit purposes.'
                            )
                            ->rows(4)
                            ->maxLength(3000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Donor')
                    ->description(
                        'Link the gift to an existing church member or enter the donor’s details manually.'
                    )
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_anonymous')
                            ->label('Anonymous Donation')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(
                                function (Set $set, bool $state): void {
                                    if (! $state) {
                                        return;
                                    }

                                    $set('member_id', null);
                                    $set('donor_name', null);
                                    $set('donor_email', null);
                                    $set('donor_phone', null);
                                    $set('address_line_1', null);
                                    $set('address_line_2', null);
                                    $set('city', null);
                                    $set('county', null);
                                    $set('postcode', null);
                                    $set('country', null);
                                    $set('gift_aid', false);
                                }
                            )
                            ->columnSpanFull(),

                        Select::make('member_id')
                            ->label('Church Member')
                            ->relationship(
                                name: 'member',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->orderBy('id')
                            )
                            ->getOptionLabelFromRecordUsing(
                                function (Member $record): string {
                                    $name = collect([
                                        $record->first_name ?? null,
                                        $record->middle_name ?? null,
                                        $record->last_name ?? null,
                                    ])
                                        ->filter()
                                        ->implode(' ');

                                    if (blank($name)) {
                                        $name = $record->name
                                            ?? $record->full_name
                                            ?? 'Member #'.$record->getKey();
                                    }

                                    if (filled($record->email ?? null)) {
                                        return $name.' — '.$record->email;
                                    }

                                    return $name;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->visible(
                                fn (Get $get): bool => ! $get('is_anonymous')
                            )
                            ->afterStateUpdated(
                                function (Set $set, ?int $state): void {
                                    if (! $state) {
                                        return;
                                    }

                                    $member = Member::query()->find($state);

                                    if (! $member) {
                                        return;
                                    }

                                    $name = collect([
                                        $member->first_name ?? null,
                                        $member->middle_name ?? null,
                                        $member->last_name ?? null,
                                    ])
                                        ->filter()
                                        ->implode(' ');

                                    if (blank($name)) {
                                        $name = $member->name
                                            ?? $member->full_name
                                            ?? null;
                                    }

                                    $set('donor_name', $name);
                                    $set(
                                        'donor_email',
                                        $member->email ?? null
                                    );
                                    $set(
                                        'donor_phone',
                                        $member->phone
                                            ?? $member->mobile
                                            ?? $member->telephone
                                            ?? null
                                    );
                                }
                            )
                            ->columnSpanFull(),

                        TextInput::make('donor_name')
                            ->label('Donor Name')
                            ->maxLength(255)
                            ->visible(
                                fn (Get $get): bool => ! $get('is_anonymous')
                            )
                            ->required(
                                fn (Get $get): bool => ! $get('is_anonymous')
                                    && blank($get('member_id'))
                            ),

                        TextInput::make('donor_email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->visible(
                                fn (Get $get): bool => ! $get('is_anonymous')
                            ),

                        TextInput::make('donor_phone')
                            ->label('Telephone')
                            ->tel()
                            ->maxLength(50)
                            ->visible(
                                fn (Get $get): bool => ! $get('is_anonymous')
                            ),
                    ]),

                Section::make('Gift Aid')
                    ->description(
                        'Gift Aid requires sufficient donor identity and home-address information.'
                    )
                    ->columns(2)
                    ->visible(
                        fn (Get $get): bool => ! $get('is_anonymous')
                    )
                    ->schema([
                        Toggle::make('gift_aid')
                            ->label('Gift Aid Declaration Applies')
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('address_line_1')
                            ->label('Address Line 1')
                            ->maxLength(255)
                            ->visible(
                                fn (Get $get): bool => $get('gift_aid')
                            )
                            ->required(
                                fn (Get $get): bool => (bool) $get('gift_aid')
                            ),

                        TextInput::make('address_line_2')
                            ->label('Address Line 2')
                            ->maxLength(255)
                            ->visible(
                                fn (Get $get): bool => $get('gift_aid')
                            ),

                        TextInput::make('city')
                            ->label('Town or City')
                            ->maxLength(255)
                            ->visible(
                                fn (Get $get): bool => $get('gift_aid')
                            )
                            ->required(
                                fn (Get $get): bool => (bool) $get('gift_aid')
                            ),

                        TextInput::make('county')
                            ->label('County')
                            ->maxLength(255)
                            ->visible(
                                fn (Get $get): bool => $get('gift_aid')
                            ),

                        TextInput::make('postcode')
                            ->label('Postcode')
                            ->maxLength(32)
                            ->visible(
                                fn (Get $get): bool => $get('gift_aid')
                            )
                            ->required(
                                fn (Get $get): bool => (bool) $get('gift_aid')
                            ),

                        Select::make('country')
                            ->label('Country')
                            ->options([
                                'GB' => 'United Kingdom',
                                'IE' => 'Ireland',
                                'NG' => 'Nigeria',
                                'US' => 'United States',
                                'CA' => 'Canada',
                                'GH' => 'Ghana',
                                'ZA' => 'South Africa',
                                'KE' => 'Kenya',
                                'OTHER' => 'Other',
                            ])
                            ->default('GB')
                            ->searchable()
                            ->native(false)
                            ->visible(
                                fn (Get $get): bool => $get('gift_aid')
                            )
                            ->required(
                                fn (Get $get): bool => (bool) $get('gift_aid')
                            ),
                    ]),

                Section::make('Recurring Donation')
                    ->description(
                        'Use these fields for standing orders, subscriptions and other repeating gifts.'
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Toggle::make('is_recurring')
                            ->label('Recurring Donation')
                            ->default(false)
                            ->live(),

                        Select::make('recurring_interval')
                            ->label('Recurring Interval')
                            ->options([
                                'weekly' => 'Weekly',
                                'fortnightly' => 'Fortnightly',
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'yearly' => 'Yearly',
                            ])
                            ->native(false)
                            ->visible(
                                fn (Get $get): bool => $get('is_recurring')
                            )
                            ->required(
                                fn (Get $get): bool => (bool) $get('is_recurring')
                            ),
                    ]),

                Section::make('Stripe and Provider Information')
                    ->description(
                        'These values are normally populated automatically by Stripe or another payment integration.'
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('stripe_session_id')
                            ->label('Stripe Checkout Session ID')
                            ->maxLength(255),

                        TextInput::make('stripe_payment_intent_id')
                            ->label('Stripe Payment Intent ID')
                            ->maxLength(255),

                        TextInput::make('stripe_subscription_id')
                            ->label('Stripe Subscription ID')
                            ->maxLength(255),

                        TextInput::make('stripe_customer_id')
                            ->label('Stripe Customer ID')
                            ->maxLength(255),

                        Textarea::make('provider_metadata')
                            ->label('Provider Metadata')
                            ->helperText(
                                'Enter valid JSON only when manually maintaining provider data.'
                            )
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Section::make('Legacy Import Information')
                    ->description(
                        'Reference values retained from OviBase migration records.'
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('legacy_ovibase_id')
                            ->label('Legacy OviBase ID')
                            ->maxLength(30),

                        TextInput::make('legacy_tenant_id')
                            ->label('Legacy Tenant ID')
                            ->maxLength(30),
                    ]),
            ]);
    }
}