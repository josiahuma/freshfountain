<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\AttendanceServiceType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service details')
                ->columns(2)
                ->schema([
                    Select::make('service_type_id')
                        ->label('Service or event')
                        ->options(fn (): array => AttendanceServiceType::query()
                            ->ordered()
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Manage this list under Attendance → Service Types.'),

                    DatePicker::make('service_date')
                        ->label('Service date')
                        ->default(now())
                        ->required()
                        ->native(false),
                ]),

            Section::make('Attendance figures')
                ->description('Attendance is recorded as totals only and is not linked to individual members.')
                ->columns(5)
                ->schema([
                    self::numberField('men', 'Men'),
                    self::numberField('women', 'Women'),
                    self::numberField('children', 'Children'),
                    self::numberField('visitors', 'Visitors'),
                    self::numberField('online', 'Online'),

                    Placeholder::make('calculated_total')
                        ->label('Calculated total')
                        ->content(fn (Get $get): string => number_format(
                            collect(['men', 'women', 'children', 'visitors', 'online'])
                                ->sum(fn (string $field): int => max(0, (int) ($get($field) ?? 0)))
                        ))
                        ->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->collapsed()
                ->schema([
                    Textarea::make('notes')
                        ->rows(4)
                        ->maxLength(3000)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    private static function numberField(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->numeric()
            ->integer()
            ->minValue(0)
            ->default(0)
            ->required()
            ->live(onBlur: true);
    }
}
