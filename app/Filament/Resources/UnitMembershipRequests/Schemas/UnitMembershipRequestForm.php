<?php

namespace App\Filament\Resources\UnitMembershipRequests\Schemas;

use App\Models\ChurchUnit;
use App\Models\Leader;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitMembershipRequestForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([

                Section::make('Applicant')
                    ->schema([

                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('last_name')
                            ->maxLength(100),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('mobile_number')
                            ->tel()
                            ->maxLength(30),

                    ])
                    ->columns(2),

                Section::make('Unit Request')
                    ->schema([

                        Select::make('church_unit_id')
                            ->label('Requested Unit')
                            ->relationship(
                                'churchUnit',
                                'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('assigned_leader_id')
                            ->label('Assigned Leader')
                            ->relationship(
                                name: 'assignedLeader',
                                titleAttribute: 'first_name',
                                modifyQueryUsing: fn ($query) =>
                                    $query
                                        ->where('is_active', true)
                                        ->orderBy('first_name')
                                        ->orderBy('last_name')
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (\App\Models\Leader $record): string =>
                                    $record->display_name
                            )
                            ->searchable([
                                'first_name',
                                'middle_name',
                                'last_name',
                                'email',
                            ])
                            ->preload()
                            ->native(false),

                        Textarea::make('message')
                            ->rows(5)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                Section::make('Internal Notes')
                    ->schema([

                        Textarea::make('internal_notes')
                            ->rows(6)
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}