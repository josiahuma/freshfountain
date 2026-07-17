<?php

namespace App\Filament\Resources\Leaders\Schemas;

use App\Models\ChurchUnit;
use App\Models\Member;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Leader Details')
                    ->description(
                        'Core identity and leadership information.'
                    )
                    ->columns(3)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('middle_name')
                            ->label('Middle name')
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Last name')
                            ->maxLength(255),

                        TextInput::make('leadership_role')
                            ->label('Leadership role')
                            ->required()
                            ->default('Unit Leader')
                            ->placeholder(
                                'For example: Unit Leader, Coordinator, Ministry Lead'
                            )
                            ->maxLength(255),

                        Select::make('church_unit_id')
                            ->label('Church unit')
                            ->options(
                                fn (): array =>
                                    ChurchUnit::query()
                                        ->active()
                                        ->ordered()
                                        ->pluck('name', 'id')
                                        ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Toggle::make('is_active')
                            ->label('Active leader')
                            ->default(true),
                    ]),

                Section::make('Contact Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('mobile_number')
                            ->label('Mobile number')
                            ->tel()
                            ->maxLength(50),
                    ]),

                Section::make('Member Link')
                    ->description(
                        'Optionally link this leadership record to an existing CRM member.'
                    )
                    ->schema([
                        Select::make('member_id')
                            ->label('Linked member')
                            ->helperText(
                                'This avoids duplicating the person while keeping leadership information separate.'
                            )
                            ->options(
                                fn (): array =>
                                    Member::query()
                                        ->orderBy('first_name')
                                        ->orderBy('last_name')
                                        ->get()
                                        ->mapWithKeys(
                                            fn (Member $member): array => [
                                                $member->id =>
                                                    $member->display_name
                                                    . (
                                                        $member->email
                                                            ? " — {$member->email}"
                                                            : ''
                                                    ),
                                            ]
                                        )
                                        ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->unique(
                                table: 'leaders',
                                column: 'member_id',
                                ignoreRecord: true
                            ),
                    ]),

                Section::make('Leadership Dates')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        DatePicker::make('started_at')
                            ->label('Started')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection(),

                        DatePicker::make('ended_at')
                            ->label('Ended')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->afterOrEqual('started_at')
                            ->closeOnDateSelection(),
                    ]),

                Section::make('Administrative Notes')
                    ->description(
                        'General operational notes only. Do not store sensitive pastoral or safeguarding information here.'
                    )
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->rows(5)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ]),

                Hidden::make('legacy_source'),
                Hidden::make('legacy_id'),
                Hidden::make('legacy_payload'),
            ]);
    }
}