<?php

namespace App\Filament\Resources\ChurchUnits\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ChurchUnitForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                Section::make('Unit Details')
                    ->description(
                        'Core information about this church ministry or operational unit.'
                    )
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Unit name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                function (
                                    ?string $state,
                                    callable $set
                                ): void {
                                    $set(
                                        'slug',
                                        Str::slug(
                                            (string) $state
                                        )
                                    );
                                }
                            ),

                        TextInput::make('alias')
                            ->label('Public alias')
                            ->helperText(
                                'For example, Heralds may use the alias Media & Tech.'
                            )
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->required()
                            ->unique(
                                ignoreRecord: true
                            )
                            ->maxLength(255),

                        TextInput::make('sort_order')
                            ->label('Display order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),

                Section::make('Contact and Meetings')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),

                        Select::make('meeting_day')
                            ->options([
                                'Monday' => 'Monday',
                                'Tuesday' => 'Tuesday',
                                'Wednesday' => 'Wednesday',
                                'Thursday' => 'Thursday',
                                'Friday' => 'Friday',
                                'Saturday' => 'Saturday',
                                'Sunday' => 'Sunday',
                                'Various' => 'Various',
                            ])
                            ->searchable()
                            ->native(false),

                        TextInput::make('meeting_time')
                            ->placeholder(
                                'For example, 7:00 PM'
                            )
                            ->maxLength(50),

                        TextInput::make(
                            'meeting_location'
                        )
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Hidden::make('legacy_source'),
                Hidden::make('legacy_id'),
                Hidden::make('legacy_payload'),
            ]);
    }
}