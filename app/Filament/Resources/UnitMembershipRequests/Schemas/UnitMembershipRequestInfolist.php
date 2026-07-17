<?php

namespace App\Filament\Resources\UnitMembershipRequests\Schemas;

use App\Models\UnitMembershipRequest;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UnitMembershipRequestInfolist
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([

                Section::make('Applicant')
                    ->schema([

                        TextEntry::make('display_name')
                            ->label('Applicant')
                            ->weight('bold'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('mobile_number')
                            ->label('Mobile')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('member.display_name')
                            ->label('Linked Member')
                            ->placeholder('Not linked'),

                    ])
                    ->columns(2),

                Section::make('Unit Request')
                    ->schema([

                        TextEntry::make('churchUnit.name')
                            ->label('Requested Unit')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('assignedLeader.display_name')
                            ->label('Assigned Leader')
                            ->placeholder('Unassigned'),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {

                                'pending' => 'warning',

                                'assigned' => 'info',

                                'contacted' => 'primary',

                                'approved' => 'success',

                                'completed' => 'success',

                                'declined' => 'danger',

                                'withdrawn' => 'gray',

                                default => 'gray',

                            }),

                        TextEntry::make('submission_reference')
                            ->label('Reference')
                            ->copyable(),

                    ])
                    ->columns(2),

                Section::make('Applicant Message')
                    ->schema([

                        TextEntry::make('message')
                            ->placeholder('No message provided.')
                            ->prose()
                            ->columnSpanFull(),

                    ]),

                Section::make('Internal Notes')
                    ->schema([

                        TextEntry::make('internal_notes')
                            ->placeholder('No internal notes.')
                            ->prose()
                            ->columnSpanFull(),

                    ]),

                Section::make('Workflow Timeline')
                    ->schema([

                        TextEntry::make('submitted_at')
                            ->label('Submitted')
                            ->since()
                            ->placeholder('-'),

                        TextEntry::make('assigned_at')
                            ->label('Assigned')
                            ->since()
                            ->placeholder('-'),

                        TextEntry::make('contacted_at')
                            ->label('Contacted')
                            ->since()
                            ->placeholder('-'),

                        TextEntry::make('approved_at')
                            ->label('Approved')
                            ->since()
                            ->placeholder('-'),

                        TextEntry::make('completed_at')
                            ->label('Completed')
                            ->since()
                            ->placeholder('-'),

                        TextEntry::make('declined_at')
                            ->label('Declined')
                            ->since()
                            ->placeholder('-'),

                        TextEntry::make('reviewer.name')
                            ->label('Reviewed By')
                            ->placeholder('-'),

                    ])
                    ->columns(2),

            ]);
    }
}