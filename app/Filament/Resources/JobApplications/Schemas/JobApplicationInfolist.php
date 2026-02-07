<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // =========================
                // Applicant
                // =========================
                Section::make('Applicant')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('jobListing.title')
                            ->label('Job')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('full_name')
                            ->label('Full name')
                            ->placeholder('-'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),

                        TextEntry::make('phone')
                            ->label('Phone')
                            ->placeholder('-'),

                        TextEntry::make('submitted_at')
                            ->label('Submitted at')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                    ]),

                // =========================
                // Personal details
                // =========================
                Section::make('Personal details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('answers.dob')
                            ->label('Date of birth')
                            ->state(fn ($record) => data_get(self::answers($record), 'dob'))
                            ->placeholder('-'),

                        TextEntry::make('answers.ni_number')
                            ->label('NI number')
                            ->state(fn ($record) => data_get(self::answers($record), 'ni_number'))
                            ->placeholder('-'),

                        TextEntry::make('answers.address')
                            ->label('Address')
                            ->state(fn ($record) => data_get(self::answers($record), 'address'))
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),

                // =========================
                // General information
                // =========================
                Section::make('General information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('answers.right_to_work')
                            ->label('Right to work in the UK?')
                            ->state(fn ($record) => self::yesNo(data_get(self::answers($record), 'right_to_work')))
                            ->placeholder('-'),

                        TextEntry::make('answers.dbs_status')
                            ->label('DBS status')
                            ->state(fn ($record) => self::prettyEnum(data_get(self::answers($record), 'dbs_status')))
                            ->placeholder('-'),

                        TextEntry::make('answers.care_experience')
                            ->label('Care experience')
                            ->state(fn ($record) => self::prettyEnum(data_get(self::answers($record), 'care_experience')))
                            ->placeholder('-'),

                        TextEntry::make('answers.preferred_role')
                            ->label('Preferred role')
                            ->state(fn ($record) => self::prettyEnum(data_get(self::answers($record), 'preferred_role')))
                            ->placeholder('-'),

                        TextEntry::make('answers.availability')
                            ->label('Availability')
                            ->state(fn ($record) => self::prettyEnum(data_get(self::answers($record), 'availability')))
                            ->placeholder('-'),

                        TextEntry::make('answers.start_date')
                            ->label('Start date')
                            ->state(fn ($record) => data_get(self::answers($record), 'start_date'))
                            ->placeholder('-'),

                        TextEntry::make('answers.why_role')
                            ->label('Why do you want this role?')
                            ->state(fn ($record) => data_get(self::answers($record), 'why_role'))
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),

                // =========================
                // References
                // =========================
                Section::make('Reference')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('answers.ref1_name')
                            ->label('Reference name')
                            ->state(fn ($record) => data_get(self::answers($record), 'ref1_name'))
                            ->placeholder('-'),

                        TextEntry::make('answers.ref1_relationship')
                            ->label('Relationship')
                            ->state(fn ($record) => data_get(self::answers($record), 'ref1_relationship'))
                            ->placeholder('-'),

                        TextEntry::make('answers.ref1_phone')
                            ->label('Phone')
                            ->state(fn ($record) => data_get(self::answers($record), 'ref1_phone'))
                            ->placeholder('-'),

                        TextEntry::make('answers.ref1_email')
                            ->label('Email')
                            ->state(fn ($record) => data_get(self::answers($record), 'ref1_email'))
                            ->placeholder('-'),
                    ]),

                // =========================
                // Declaration
                // =========================
                Section::make('Declaration')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('answers.declare_truth')
                            ->label('Truth declaration')
                            ->state(fn ($record) => self::yesNo(data_get(self::answers($record), 'declare_truth')))
                            ->placeholder('-'),

                        TextEntry::make('answers.declare_safeguarding')
                            ->label('Safeguarding consent')
                            ->state(fn ($record) => self::yesNo(data_get(self::answers($record), 'declare_safeguarding')))
                            ->placeholder('-'),

                        TextEntry::make('answers.signature')
                            ->label('Signature')
                            ->state(fn ($record) => data_get(self::answers($record), 'signature'))
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),
            ]);
    }

    /**
     * answers may be stored as array or JSON string depending on project.
     */
    private static function answers($record): array
    {
        $state = $record->answers ?? [];

        if (is_string($state)) {
            $decoded = json_decode($state, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($state) ? $state : [];
    }

    private static function yesNo($value): string
    {
        if (is_bool($value)) return $value ? 'Yes' : 'No';

        $v = strtolower((string) $value);
        if (in_array($v, ['1', 'yes', 'true', 'y', 'on'], true)) return 'Yes';
        if (in_array($v, ['0', 'no', 'false', 'n', 'off'], true)) return 'No';

        return (string) $value ?: '-';
    }

    private static function prettyEnum($value): string
    {
        if (is_null($value) || $value === '') return '-';

        // Make "5_plus" => "5 Plus", "on_update_service" => "On Update Service"
        return ucwords(str_replace('_', ' ', (string) $value));
    }
}
