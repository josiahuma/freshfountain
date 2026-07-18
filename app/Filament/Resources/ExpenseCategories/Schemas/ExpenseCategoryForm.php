<?php

namespace App\Filament\Resources\ExpenseCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Expense Category Details')
                    ->description(
                        'Create a category for recording church expenses and outgoing payments.'
                    )
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->placeholder('e.g. Utilities')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->autofocus(),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->helperText(
                                'Lower numbers appear first in category lists.'
                            )
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder(
                                'Describe the type of expense recorded under this category.'
                            )
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText(
                                'Inactive categories will not be available when recording new expenses.'
                            )
                            ->default(true),
                    ]),
            ]);
    }
}