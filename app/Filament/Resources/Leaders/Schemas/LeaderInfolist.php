<?php

namespace App\Filament\Resources\Leaders\Schemas;

use App\Models\Leader;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Leader Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Leader')
                            ->state(
                                fn (Leader $record): string =>
                                    $record->full_name
                            )
                            ->weight('bold')
                            ->columnSpan(2),

                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),

                        TextEntry::make(
                            'leadership_role'
                        )
                            ->label('Role')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make(
                            'churchUnit.name'
                        )
                            ->label('Church Unit')
                            ->placeholder(
                                'Not assigned'
                            )
                            ->badge()
                            ->color('info'),

                        TextEntry::make(
                            'assigned_members_count'
                        )
                            ->label(
                                'Assigned Members'
                            )
                            ->state(
                                fn (Leader $record): int =>
                                    $record
                                        ->assignedMembers()
                                        ->count()
                            ),
                    ]),

                Section::make('Contact Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('email')
                            ->placeholder(
                                'Not provided'
                            )
                            ->copyable(),

                        TextEntry::make(
                            'mobile_number'
                        )
                            ->label('Mobile number')
                            ->placeholder(
                                'Not provided'
                            )
                            ->copyable(),
                    ]),

                Section::make('CRM Links')
                    ->columns(2)
                    ->schema([
                        TextEntry::make(
                            'member.display_name'
                        )
                            ->label(
                                'Linked Member'
                            )
                            ->state(
                                fn (
                                    Leader $record
                                ): ?string =>
                                    $record
                                        ->member
                                        ?->display_name
                            )
                            ->placeholder(
                                'No linked member'
                            ),

                        TextEntry::make(
                            'member.email'
                        )
                            ->label(
                                'Member Account Email'
                            )
                            ->placeholder(
                                'Not available'
                            ),
                    ]),

                Section::make('Leadership Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('started_at')
                            ->label('Started')
                            ->date('d M Y')
                            ->placeholder(
                                'Not provided'
                            ),

                        TextEntry::make('ended_at')
                            ->label('Ended')
                            ->date('d M Y')
                            ->placeholder(
                                'Still active'
                            ),
                    ]),

                Section::make(
                    'Administrative Notes'
                )
                    ->collapsed()
                    ->schema([
                        TextEntry::make('notes')
                            ->placeholder(
                                'No notes recorded'
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make('Legacy Migration')
                    ->description(
                        'Internal migration information retained from OviBase.'
                    )
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextEntry::make(
                            'legacy_source'
                        )
                            ->label('Source'),

                        TextEntry::make(
                            'legacy_id'
                        )
                            ->label(
                                'Legacy record ID'
                            ),

                        TextEntry::make(
                            'created_at'
                        )
                            ->label('Created')
                            ->dateTime(
                                'd M Y, H:i'
                            ),

                        TextEntry::make(
                            'updated_at'
                        )
                            ->label('Updated')
                            ->dateTime(
                                'd M Y, H:i'
                            ),
                    ])
                    ->visible(
                        fn (Leader $record): bool =>
                            filled(
                                $record->legacy_source
                            )
                    ),
            ]);
    }
}