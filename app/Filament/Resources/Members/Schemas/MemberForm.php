<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\ChurchUnit;
use App\Models\Member;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Leader;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Details')
                    ->description(
                        'Basic personal information for the church member.'
                    )
                    ->columns(3)
                    ->schema([
                        Select::make('title')
                            ->options([
                                'Mr' => 'Mr',
                                'Mrs' => 'Mrs',
                                'Miss' => 'Miss',
                                'Ms' => 'Ms',
                                'Dr' => 'Dr',
                                'Pastor' => 'Pastor',
                                'Minister' => 'Minister',
                                'Deacon' => 'Deacon',
                                'Deaconess' => 'Deaconess',
                                'Elder' => 'Elder',
                                'Reverend' => 'Reverend',
                            ])
                            ->searchable()
                            ->native(false),

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

                        Select::make('gender')
                            ->options(
                                Member::genderOptions()
                            )
                            ->native(false),

                        DatePicker::make('date_of_birth')
                            ->label('Date of birth')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->closeOnDateSelection(),

                        DatePicker::make('anniversary_date')
                            ->label('Wedding anniversary')
                            ->helperText(
                                'Optional. Store the anniversary date only; no family relationship is created.'
                            )
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection(),
                    ]),

                Section::make('Contact Details')
                    ->description(
                        'Contact information and postal address.'
                    )
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('mobile_number')
                            ->label('Mobile number')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('alternative_phone')
                            ->label('Alternative phone')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('postcode')
                            ->maxLength(30),

                        Textarea::make('address')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Church Information')
                    ->description(
                        'Membership status, church unit and service information.'
                    )
                    ->columns(2)
                    ->schema([
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

                        Select::make('membership_status')
                            ->label('Membership status')
                            ->options(
                                Member::statusOptions()
                            )
                            ->required()
                            ->default(
                                Member::STATUS_ACTIVE
                            )
                            ->native(false),

                        DatePicker::make('joined_at')
                            ->label('Date joined')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection(),

                        Toggle::make('is_active')
                            ->label('Active record')
                            ->helperText(
                                'Turn this off to archive the member without deleting their record.'
                            )
                            ->default(true),

                        Select::make('leader_id')
                            ->label('Assigned leader')
                            ->helperText(
                                'Optional. Select the leader responsible for this member.'
                            )
                            ->options(
                                fn (): array =>
                                    Leader::query()
                                        ->active()
                                        ->orderBy('first_name')
                                        ->orderBy('last_name')
                                        ->get()
                                        ->mapWithKeys(
                                            fn (Leader $leader): array => [
                                                $leader->id =>
                                                    $leader->display_name
                                                    . (
                                                        $leader->churchUnit
                                                            ? " — {$leader->churchUnit->name}"
                                                            : ''
                                                    ),
                                            ]
                                        )
                                        ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('user_id')
                            ->label('Linked website/LMS account')
                            ->helperText(
                                'Optional. Linking does not grant CRM or admin access.'
                            )
                            ->options(
                                fn (): array =>
                                    User::query()
                                        ->orderBy('name')
                                        ->get()
                                        ->mapWithKeys(
                                            fn (User $user): array => [
                                                $user->id =>
                                                    "{$user->name} — {$user->email}",
                                            ]
                                        )
                                        ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->unique(
                                table: 'members',
                                column: 'user_id',
                                ignoreRecord: true
                            ),
                    ]),

                Section::make('Communication Preferences')
                    ->description(
                        'Control whether this member may receive church communications.'
                    )
                    ->columns(3)
                    ->schema([
                        Toggle::make('email_consent')
                            ->label('Email consent')
                            ->default(true),

                        Toggle::make('sms_consent')
                            ->label('SMS consent')
                            ->default(true),

                        Toggle::make('do_not_contact')
                            ->label('Do not contact')
                            ->helperText(
                                'Overrides both email and SMS consent.'
                            )
                            ->default(false)
                            ->live(),
                    ]),

                Section::make('Administrative Notes')
                    ->description(
                        'Internal CRM notes. Do not store highly sensitive safeguarding or pastoral information here.'
                    )
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label('General notes')
                            ->rows(5)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ]),

                /*
                |--------------------------------------------------------------------------
                | Legacy import fields
                |--------------------------------------------------------------------------
                |
                | These are managed by the OviBase importer and intentionally hidden
                | from normal CRM administrators.
                |
                */

                Hidden::make('legacy_church_leader_name'),
                Hidden::make('legacy_source'),
                Hidden::make('legacy_id'),
                Hidden::make('legacy_payload'),
            ]);
    }
}