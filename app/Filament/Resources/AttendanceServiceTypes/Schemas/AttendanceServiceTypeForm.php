<?php

namespace App\Filament\Resources\AttendanceServiceTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class AttendanceServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service type')
                ->description('These options appear in the usher attendance form and attendance administration form.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(191)
                        ->unique(ignoreRecord: true),

                    TextInput::make('sort_order')
                        ->label('Display order')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Available for attendance entry')
                        ->default(true)
                        ->helperText('Inactive service types remain attached to historical records but disappear from the usher form.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
