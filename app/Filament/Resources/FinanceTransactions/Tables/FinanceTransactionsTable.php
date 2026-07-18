<?php

namespace App\Filament\Resources\FinanceTransactions\Tables;

use App\Models\FinanceTransaction;
use App\Support\Finance\Money;
use App\Support\Finance\PaymentMethods;
use App\Support\Finance\TransactionStatuses;
use App\Support\Finance\TransactionTypes;
use App\Support\Privacy\DonorPrivacy;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FinanceTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('transaction_date', 'desc')
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => TransactionTypes::INCOME,
                        'danger' => TransactionTypes::EXPENSE,
                        'warning' => TransactionTypes::GIFT_AID,
                        'gray' => TransactionTypes::ADJUSTMENT,
                    ])
                    ->formatStateUsing(
                        fn (?string $state) => TransactionTypes::label($state)
                    )
                    ->sortable(),

                TextColumn::make('description')
                    ->formatStateUsing(
                        fn (?string $state, FinanceTransaction $record): ?string =>
                            DonorPrivacy::transactionDescription($state, $record)
                    )
                    ->searchable(DonorPrivacy::canViewIdentity())
                    ->sortable()
                    ->weight('bold')
                    ->limit(45),

                TextColumn::make('reference')
                    ->searchable()
                    ->toggleable(),

                Money::column('amount'),

                TextColumn::make('incomeCategory.name')
                    ->label('Income Category')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expenseCategory.name')
                    ->label('Expense Category')
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('payment_method')
                    ->label('Payment')
                    ->colors([
                        'success' => PaymentMethods::CASH,
                        'info' => [
                            PaymentMethods::BANK_TRANSFER,
                            PaymentMethods::ONLINE,
                        ],
                        'warning' => PaymentMethods::CARD,
                        'primary' => [
                            PaymentMethods::DIRECT_DEBIT,
                            PaymentMethods::STANDING_ORDER,
                        ],
                        'gray' => [
                            PaymentMethods::CHEQUE,
                            PaymentMethods::OTHER,
                        ],
                    ])
                    ->formatStateUsing(
                        fn (?string $state) => PaymentMethods::label($state)
                    )
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => TransactionStatuses::COMPLETED,
                        'warning' => TransactionStatuses::PENDING,
                        'danger' => TransactionStatuses::CANCELLED,
                        'gray' => TransactionStatuses::REFUNDED,
                    ])
                    ->formatStateUsing(
                        fn (?string $state) => TransactionStatuses::label($state)
                    )
                    ->sortable(),

                BadgeColumn::make('source')
                    ->colors([
                        'primary' => 'manual',
                        'success' => 'donation',
                        'warning' => 'gift_aid',
                        'gray' => 'adjustment',
                        'info' => 'import',
                    ])
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(TransactionTypes::options()),

                SelectFilter::make('income_category_id')
                    ->relationship('incomeCategory', 'name')
                    ->label('Income Category'),

                SelectFilter::make('expense_category_id')
                    ->relationship('expenseCategory', 'name')
                    ->label('Expense Category'),

                SelectFilter::make('status')
                    ->options(TransactionStatuses::options()),

                TernaryFilter::make('donation_id')
                    ->label('Linked Donation')
                    ->nullable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
