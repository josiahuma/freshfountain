<?php

namespace App\Filament\Resources\ChurchUnits\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChurchUnitInfolist
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                Section::make('Unit Overview')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Unit name')
                            ->weight('bold'),

                        TextEntry::make('alias')
                            ->placeholder('No alias'),

                        TextEntry::make('slug'),

                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),

                        TextEntry::make('description')
                            ->placeholder(
                                'No description provided'
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Contact and Meetings'
                )
                    ->columns(2)
                    ->schema([
                        TextEntry::make('email')
                            ->placeholder('Not set'),

                        TextEntry::make('phone')
                            ->placeholder('Not set'),

                        TextEntry::make('meeting_day')
                            ->placeholder('Not set'),

                        TextEntry::make('meeting_time')
                            ->placeholder('Not set'),

                        TextEntry::make(
                            'meeting_location'
                        )
                            ->placeholder('Not set')
                            ->columnSpanFull(),
                    ]),

                Section::make('CRM Summary')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('members_count')
                            ->label('Members')
                            ->state(
                                fn ($record): int =>
                                    $record
                                        ->members()
                                        ->count()
                            ),

                        TextEntry::make('leaders_count')
                            ->label('Leaders')
                            ->state(
                                fn ($record): int =>
                                    $record
                                        ->leaders()
                                        ->count()
                            ),
                    ]),
            ]);
    }
}