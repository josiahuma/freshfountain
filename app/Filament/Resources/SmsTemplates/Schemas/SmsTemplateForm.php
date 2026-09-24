<?php

namespace App\Filament\Resources\SmsTemplates\Schemas;

use App\Models\SmsTemplate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SmsTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('SMS Template')
                ->description('Create reusable messages for birthdays, service reminders, announcements and general communication.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    Select::make('category')->options(SmsTemplate::categoryOptions())->default(SmsTemplate::CATEGORY_GENERAL)->required()->native(false),
                    Textarea::make('body')
                        ->label('Message')
                        ->required()
                        ->rows(6)
                        ->maxLength(1500)
                        ->helperText('Personalisation available: {first_name}, {last_name}, {name}, {full_name}.')
                        ->columnSpanFull(),
                    Toggle::make('is_active')->label('Active')->default(true),
                    Toggle::make('is_birthday')
                        ->label('Birthday template')
                        ->helperText('The dashboard birthday action uses the most recently updated active template marked as Birthday template.')
                        ->default(false),
                ]),
        ]);
    }
}
