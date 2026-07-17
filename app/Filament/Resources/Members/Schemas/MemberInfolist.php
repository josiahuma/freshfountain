<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\Leader;
use App\Models\Member;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Member Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Member')
                            ->state(
                                fn (Member $record): string =>
                                    $record->full_name
                            )
                            ->weight('bold')
                            ->columnSpan(2),

                        TextEntry::make(
                            'membership_status'
                        )
                            ->label('Status')
                            ->formatStateUsing(
                                fn (?string $state): string =>
                                    Member::statusOptions()[
                                        $state
                                    ] ?? ucfirst(
                                        $state ?? 'Unknown'
                                    )
                            )
                            ->badge()
                            ->color(
                                fn (?string $state): string =>
                                    match ($state) {
                                        Member::STATUS_ACTIVE =>
                                            'success',

                                        Member::STATUS_VISITOR =>
                                            'info',

                                        Member::STATUS_PENDING =>
                                            'warning',

                                        Member::STATUS_INACTIVE =>
                                            'gray',

                                        Member::STATUS_LEFT =>
                                            'danger',

                                        default =>
                                            'gray',
                                    }
                            ),

                        TextEntry::make('gender')
                            ->formatStateUsing(
                                fn (?string $state): string =>
                                    Member::genderOptions()[
                                        $state
                                    ] ?? ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $state
                                                ?? 'Not provided'
                                        )
                                    )
                            )
                            ->placeholder(
                                'Not provided'
                            ),

                        TextEntry::make(
                            'date_of_birth'
                        )
                            ->label('Date of birth')
                            ->date('d M Y')
                            ->placeholder(
                                'Not provided'
                            ),

                        TextEntry::make(
                            'anniversary_date'
                        )
                            ->label(
                                'Wedding anniversary'
                            )
                            ->date('d M')
                            ->placeholder(
                                'Not provided'
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

                        TextEntry::make(
                            'alternative_phone'
                        )
                            ->label(
                                'Alternative phone'
                            )
                            ->placeholder(
                                'Not provided'
                            )
                            ->copyable(),

                        TextEntry::make('postcode')
                            ->placeholder(
                                'Not provided'
                            ),

                        TextEntry::make('address')
                            ->placeholder(
                                'Not provided'
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make('Church Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make(
                            'primary_unit_name'
                        )
                            ->label('Primary unit')
                            ->state(
                                fn (
                                    Member $record
                                ): ?string =>
                                    $record
                                        ->churchUnit
                                        ?->name
                            )
                            ->placeholder(
                                'Not assigned'
                            )
                            ->badge()
                            ->color('info'),

                        TextEntry::make(
                            'primary_leader_name'
                        )
                            ->label(
                                'Primary assigned leader'
                            )
                            ->state(
                                fn (
                                    Member $record
                                ): ?string =>
                                    $record
                                        ->leader
                                        ?->display_name
                            )
                            ->placeholder(
                                'Not assigned'
                            ),

                        TextEntry::make(
                            'all_unit_memberships'
                        )
                            ->label(
                                'All unit memberships'
                            )
                            ->state(
                                function (
                                    Member $record
                                ): array {
                                    $record->loadMissing(
                                        'churchUnits'
                                    );

                                    return $record
                                        ->churchUnits
                                        ->sortBy('name')
                                        ->pluck('name')
                                        ->filter()
                                        ->values()
                                        ->all();
                                }
                            )
                            ->badge()
                            ->separator(',')
                            ->placeholder(
                                'No unit memberships'
                            )
                            ->columnSpanFull(),

                        TextEntry::make(
                            'unit_leader_assignments'
                        )
                            ->label(
                                'Leader assigned for each unit'
                            )
                            ->state(
                                function (
                                    Member $record
                                ): array {
                                    $record->loadMissing(
                                        'churchUnits'
                                    );

                                    $leaderIds = $record
                                        ->churchUnits
                                        ->pluck(
                                            'pivot.assigned_leader_id'
                                        )
                                        ->filter()
                                        ->unique()
                                        ->values();

                                    $leaders = Leader::query()
                                        ->whereIn(
                                            'id',
                                            $leaderIds
                                        )
                                        ->get()
                                        ->keyBy('id');

                                    return $record
                                        ->churchUnits
                                        ->sortBy('name')
                                        ->map(
                                            function (
                                                $unit
                                            ) use (
                                                $leaders
                                            ): string {
                                                $leaderId =
                                                    $unit
                                                        ->pivot
                                                        ?->assigned_leader_id;

                                                $leader =
                                                    $leaderId
                                                        ? $leaders->get(
                                                            $leaderId
                                                        )
                                                        : null;

                                                return $unit->name
                                                    . ' — '
                                                    . (
                                                        $leader
                                                            ?->display_name
                                                        ?? 'No assigned leader'
                                                    );
                                            }
                                        )
                                        ->values()
                                        ->all();
                                }
                            )
                            ->bulleted()
                            ->placeholder(
                                'No unit leader assignments'
                            )
                            ->columnSpanFull(),

                        TextEntry::make('joined_at')
                            ->label(
                                'Primary date joined'
                            )
                            ->date('d M Y')
                            ->placeholder(
                                'Not provided'
                            ),

                        TextEntry::make(
                            'user.name'
                        )
                            ->label(
                                'Linked website account'
                            )
                            ->placeholder(
                                'No linked account'
                            ),

                        IconEntry::make('is_active')
                            ->label('Active record')
                            ->boolean(),
                    ]),

                Section::make(
                    'Communication Preferences'
                )
                    ->columns(3)
                    ->schema([
                        IconEntry::make(
                            'email_consent'
                        )
                            ->label('Email consent')
                            ->boolean(),

                        IconEntry::make(
                            'sms_consent'
                        )
                            ->label('SMS consent')
                            ->boolean(),

                        IconEntry::make(
                            'do_not_contact'
                        )
                            ->label('Do not contact')
                            ->boolean()
                            ->trueColor('danger')
                            ->falseColor('success'),
                    ]),

                Section::make(
                    'Administrative Notes'
                )
                    ->collapsed()
                    ->schema([
                        TextEntry::make('notes')
                            ->label('General notes')
                            ->placeholder(
                                'No notes recorded'
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Legacy Migration'
                )
                    ->description(
                        'Internal migration information retained from OviBase.'
                    )
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextEntry::make(
                            'legacy_source'
                        )
                            ->label('Source')
                            ->placeholder(
                                'Native CRM record'
                            ),

                        TextEntry::make(
                            'legacy_church_leader_name'
                        )
                            ->label(
                                'Legacy leader value'
                            )
                            ->placeholder('None'),

                        TextEntry::make(
                            'legacy_id'
                        )
                            ->label(
                                'Legacy record ID'
                            )
                            ->placeholder('None'),

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
                        fn (
                            Member $record
                        ): bool =>
                            filled(
                                $record->legacy_source
                            )
                    ),
            ]);
    }
}