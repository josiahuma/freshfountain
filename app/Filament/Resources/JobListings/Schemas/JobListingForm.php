<?php

namespace App\Filament\Resources\JobListings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class JobListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Job Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (! $get('slug')) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('location')
                            ->default('Essex'),

                        TextInput::make('employment_type')
                            ->label('Employment Type')
                            ->default('Full-time'),

                        TextInput::make('salary')
                            ->placeholder('£12–£15/hr'),

                        DatePicker::make('closing_date')
                            ->label('Closing Date'),

                        Select::make('template')
                            ->options([
                                'basic' => 'Basic',
                            ])
                            ->default('basic')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),

                Section::make('Description')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Job Description')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'link',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ]),
                    ]),
            ]);
    }
}
