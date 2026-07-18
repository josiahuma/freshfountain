<?php

namespace App\Filament\Resources\Donations\Tables;

use App\Models\Donation;
use App\Support\Finance\Money;
use App\Support\Finance\PaymentMethods;
use App\Support\Privacy\DonorPrivacy;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('paid_at')
                    ->label('Date')
                    ->date('d M Y')
                    ->placeholder('Not paid')
                    ->sortable(),

                TextColumn::make('donor_display')
                    ->label('Donor')
                    ->state(
                        function (Donation $record): string {
                            if ($record->is_anonymous) {
                                return 'Anonymous Donor';
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

                                if (filled($record->member->full_name ?? null)) {
                                    return DonorPrivacy::name($record->member->full_name);
                                }

                                return DonorPrivacy::canViewIdentity()
                                    ? 'Member #'.$record->member->getKey()
                                    : 'Hidden Member';
                            }

                            return DonorPrivacy::canViewIdentity() ? 'Guest Donor' : 'Hidden Donor';
                        }
                    )
                    ->description(
                        function (Donation $record): ?string {
                            if ($record->is_anonymous) {
                                return null;
                            }

                            if (filled($record->donor_email)) {
                                return DonorPrivacy::email($record->donor_email);
                            }

                            if (filled($record->donor_phone)) {
                                return DonorPrivacy::phone($record->donor_phone);
                            }

                            return null;
                        }
                    )
                    ->wrap(),

                TextColumn::make('donationFund.name')
                    ->label('Fund')
                    ->placeholder('General Fund')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Money::column('amount')
                    ->label('Amount')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->formatStateUsing(
                        fn (?string $state): string => PaymentMethods::label($state)
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string => PaymentMethods::color($state)
                    )
                    ->placeholder('Not specified')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                            'cancelled' => 'Cancelled',
                            'refunded' => 'Refunded',
                            default => filled($state)
                                ? str($state)->replace('_', ' ')->title()->toString()
                                : 'Unknown',
                        }
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'paid' => 'success',
                            'pending' => 'warning',
                            'failed' => 'danger',
                            'cancelled' => 'gray',
                            'refunded' => 'info',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                IconColumn::make('gift_aid')
                    ->label('Gift Aid')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('is_recurring')
                    ->label('Recurring')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_anonymous')
                    ->label('Anonymous')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('recurring_interval')
                    ->label('Frequency')
                    ->formatStateUsing(
                        fn (?string $state): string => filled($state)
                            ? str($state)->replace('_', ' ')->title()->toString()
                            : '—'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_provider')
                    ->label('Source')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'manual' => 'Manual Entry',
                            'stripe' => 'Stripe',
                            'bank' => 'Bank',
                            'cash' => 'Cash',
                            'cheque' => 'Cheque',
                            'ovibase' => 'OviBase Import',
                            'other' => 'Other',
                            default => filled($state)
                                ? str($state)->replace('_', ' ')->title()->toString()
                                : 'Unknown',
                        }
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'stripe' => 'primary',
                            'manual' => 'gray',
                            'bank' => 'info',
                            'cash' => 'success',
                            'cheque' => 'warning',
                            'ovibase' => 'gray',
                            default => 'gray',
                        }
                    )
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('member_id')
                    ->label('Member ID')
                    ->formatStateUsing(
                        fn (?int $state): string => $state
                            ? '#'.$state
                            : '—'
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('finance_transaction_id')
                    ->label('Ledger Entry')
                    ->formatStateUsing(
                        fn (?int $state): string => $state
                            ? '#'.$state
                            : 'Not linked'
                    )
                    ->badge()
                    ->color(
                        fn (?int $state): string => $state
                            ? 'success'
                            : 'warning'
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('donor_email')
                    ->label('Email')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::email($state))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('donor_phone')
                    ->label('Telephone')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::phone($state))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('postcode')
                    ->label('Postcode')
                    ->formatStateUsing(fn (?string $state): ?string => DonorPrivacy::text($state))
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stripe_payment_intent_id')
                    ->label('Stripe Payment Intent')
                    ->searchable()
                    ->copyable()
                    ->limit(25)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stripe_subscription_id')
                    ->label('Stripe Subscription')
                    ->searchable()
                    ->copyable()
                    ->limit(25)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('legacy_ovibase_id')
                    ->label('OviBase ID')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Recorded')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('donation_fund_id')
                    ->label('Donation Fund')
                    ->relationship(
                        name: 'donationFund',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query
                            ->orderBy('sort_order')
                            ->orderBy('name')
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options(PaymentMethods::options())
                    ->searchable(),

                SelectFilter::make('payment_provider')
                    ->label('Payment Source')
                    ->options([
                        'manual' => 'Manual Entry',
                        'stripe' => 'Stripe',
                        'bank' => 'Bank',
                        'cash' => 'Cash',
                        'cheque' => 'Cheque',
                        'ovibase' => 'OviBase Import',
                        'other' => 'Other',
                    ]),

                SelectFilter::make('recurring_interval')
                    ->label('Recurring Frequency')
                    ->options([
                        'weekly' => 'Weekly',
                        'fortnightly' => 'Fortnightly',
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'yearly' => 'Yearly',
                    ]),

                TernaryFilter::make('gift_aid')
                    ->label('Gift Aid'),

                TernaryFilter::make('is_recurring')
                    ->label('Recurring Donation'),

                TernaryFilter::make('is_anonymous')
                    ->label('Anonymous Donation'),

                TernaryFilter::make('finance_transaction_id')
                    ->label('Linked to Finance Ledger')
                    ->nullable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}