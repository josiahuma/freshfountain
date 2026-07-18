<?php

namespace App\Filament\Resources\FinanceTransactions\Schemas;

use App\Support\Finance\Money;
use App\Support\Finance\PaymentMethods;
use App\Support\Finance\TransactionStatuses;
use App\Support\Finance\TransactionTypes;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class FinanceTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Details')
                    ->description(
                        'Record incoming or outgoing church funds in the central finance ledger.'
                    )
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Transaction Type')
                            ->options(TransactionTypes::options())
                            ->default(TransactionTypes::INCOME)
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(
                                function (Set $set, ?string $state): void {
                                    if (! TransactionTypes::usesIncomeCategory($state)) {
                                        $set('income_category_id', null);
                                    }

                                    if (! TransactionTypes::usesExpenseCategory($state)) {
                                        $set('expense_category_id', null);
                                    }

                                    if ($state === TransactionTypes::GIFT_AID) {
                                        $set('source', 'gift_aid');
                                    }

                                    if ($state === TransactionTypes::ADJUSTMENT) {
                                        $set('source', 'adjustment');
                                    }

                                    if (
                                        in_array(
                                            $state,
                                            [
                                                TransactionTypes::INCOME,
                                                TransactionTypes::EXPENSE,
                                            ],
                                            true
                                        )
                                    ) {
                                        $set('source', 'manual');
                                    }
                                }
                            ),

                        DatePicker::make('transaction_date')
                            ->label('Transaction Date')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->maxDate(now()),

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

                        Select::make('income_category_id')
                            ->label('Income Category')
                            ->relationship(
                                name: 'incomeCategory',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(
                                fn (Get $get): bool => TransactionTypes::usesIncomeCategory(
                                    $get('type')
                                )
                            )
                            ->required(
                                fn (Get $get): bool => TransactionTypes::requiresIncomeCategory(
                                    $get('type')
                                )
                            ),

                        Select::make('expense_category_id')
                            ->label('Expense Category')
                            ->relationship(
                                name: 'expenseCategory',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(
                                fn (Get $get): bool => TransactionTypes::usesExpenseCategory(
                                    $get('type')
                                )
                            )
                            ->required(
                                fn (Get $get): bool => TransactionTypes::requiresExpenseCategory(
                                    $get('type')
                                )
                            ),

                        TextInput::make('description')
                            ->label('Description')
                            ->placeholder(
                                'e.g. Sunday offering or electricity bill'
                            )
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('reference')
                            ->label('Reference')
                            ->placeholder(
                                'e.g. INV-2026-001 or BANK-REF-123'
                            )
                            ->maxLength(255),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(PaymentMethods::options())
                            ->searchable()
                            ->native(false),

                        Select::make('source')
                            ->label('Source')
                            ->options([
                                'manual' => 'Manual Entry',
                                'donation' => 'Donation',
                                'gift_aid' => 'Gift Aid',
                                'adjustment' => 'Adjustment',
                                'import' => 'OviBase Import',
                            ])
                            ->default('manual')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->options(TransactionStatuses::options())
                            ->default(TransactionStatuses::COMPLETED)
                            ->required()
                            ->native(false),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->placeholder(
                                'Add any information that may be useful for finance review or auditing.'
                            )
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Linked Records')
                    ->description(
                        'These fields are mainly used by donations, integrations and imported OviBase records.'
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Select::make('donation_id')
                            ->label('Linked Donation')
                            ->relationship('donation', 'id')
                            ->searchable()
                            ->preload()
                            ->native(false),

                        TextInput::make('created_by_user_id')
                            ->label('Recorded By User ID')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('legacy_category_name')
                            ->label('Legacy Category Name')
                            ->maxLength(255),

                        TextInput::make('legacy_ovibase_id')
                            ->label('Legacy OviBase ID')
                            ->maxLength(255),

                        TextInput::make('legacy_tenant_id')
                            ->label('Legacy Tenant ID')
                            ->maxLength(255),
                    ]),
            ]);
    }
}