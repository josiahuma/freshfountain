<?php

namespace App\Filament\Resources\DonationFunds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class DonationFundForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([

                Section::make('Donation Fund')
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('Fund Name')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        Select::make('income_category_id')
                            ->label('Income Category')
                            ->relationship(
                                name: 'incomeCategory',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('is_active', true)
                                    ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Select an income category')
                            ->helperText(
                                'Donations to this fund will be posted to this income category in the finance ledger.'
                            ),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_default')
                            ->label('Default Fund')
                            ->helperText(
                                'Automatically selected on the public giving page.'
                            ),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}