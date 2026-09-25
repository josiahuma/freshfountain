<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\ChurchUnit;
use App\Models\Leader;
use App\Models\Member;
use App\Models\User;
use App\Support\Access\BackendAccess;
use App\Support\Access\BackendPermissions;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Services\Access\MemberBackendAccessService;

class MemberForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                Section::make('Record Type')
                    ->description('Person records are normal church members. Functional accounts are shared organisational identities such as Follow Up, Finance or Church Admin.')
                    ->visible(fn (): bool => BackendAccess::isSuperAdmin())
                    ->schema([
                        Select::make('record_type')
                            ->label('Record type')
                            ->options(Member::recordTypeOptions())
                            ->default(Member::TYPE_PERSON)
                            ->required()
                            ->live()
                            ->native(false),
                    ]),

                Hidden::make('record_type')
                    ->default(Member::TYPE_PERSON)
                    ->visible(fn (): bool => ! BackendAccess::isSuperAdmin()),

                Section::make(
                    'Personal Details'
                )
                    ->description(
                        'Basic personal information for the church member.'
                    )
                    ->columns(3)
                    ->schema([
                        Select::make('title')
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
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

                        TextInput::make(
                            'first_name'
                        )
                            ->label(fn ($get): string => $get('record_type') === Member::TYPE_FUNCTIONAL ? 'Account name' : 'First name')
                            ->helperText(fn ($get): ?string => $get('record_type') === Member::TYPE_FUNCTIONAL ? 'For example: Follow Up, Finance, Church Admin or Waterbrooks.' : null)
                            ->required()
                            ->maxLength(255),

                        TextInput::make(
                            'middle_name'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Middle name'
                            )
                            ->maxLength(255),

                        TextInput::make(
                            'last_name'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Last name'
                            )
                            ->maxLength(255),

                        Select::make('gender')
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->options(
                                Member::genderOptions()
                            )
                            ->native(false),

                        DatePicker::make(
                            'date_of_birth'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Date of birth'
                            )
                            ->native(false)
                            ->displayFormat(
                                'd M Y'
                            )
                            ->maxDate(now())
                            ->closeOnDateSelection(),

                        DatePicker::make(
                            'anniversary_date'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Wedding anniversary'
                            )
                            ->helperText(
                                'Optional. Store the anniversary date only; no family relationship is created.'
                            )
                            ->native(false)
                            ->displayFormat(
                                'd M Y'
                            )
                            ->closeOnDateSelection(),
                    ]),

                Section::make(
                    'Contact Details'
                )
                    ->description(
                        'Contact information and postal address.'
                    )
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label(fn ($get): string => $get('record_type') === Member::TYPE_FUNCTIONAL ? 'Enterprise / functional email' : 'Email')
                            ->email()
                            ->maxLength(255)
                            ->required(fn ($get): bool => $get('record_type') === Member::TYPE_FUNCTIONAL)
                            ->disabled(fn (?Member $record): bool => filled($record) && $record->isFunctionalAccount() && ! BackendAccess::isSuperAdmin())
                            ->helperText(fn ($get): ?string => $get('record_type') === Member::TYPE_FUNCTIONAL ? 'This email is also the /hub login email when backend access is enabled.' : null),

                        TextInput::make(
                            'mobile_number'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Mobile number'
                            )
                            ->tel()
                            ->maxLength(50),

                        TextInput::make(
                            'alternative_phone'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Alternative phone'
                            )
                            ->tel()
                            ->maxLength(50),

                        TextInput::make(
                            'postcode'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->maxLength(30),

                        Textarea::make(
                            'address'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Church Information'
                )
                    ->description(
                        'Manage the primary unit and every additional unit membership.'
                    )
                    ->columns(2)
                    ->schema([
                        Select::make(
                            'church_unit_id'
                        )
                            ->label(
                                'Primary church unit'
                            )
                            ->helperText(
                                'The main unit shown in older CRM records and reports.'
                            )
                            ->options(
                                fn (): array =>
                                    ChurchUnit::query()
                                        ->active()
                                        ->ordered()
                                        ->pluck(
                                            'name',
                                            'id'
                                        )
                                        ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live(),

                        Select::make(
                            'membership_status'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Membership status'
                            )
                            ->options(
                                Member::statusOptions()
                            )
                            ->required()
                            ->default(
                                Member::STATUS_ACTIVE
                            )
                            ->native(false),

                        CheckboxList::make(
                            'church_unit_ids'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'All church units'
                            )
                            ->helperText(
                                'Tick every unit this person belongs to. Unticking a unit removes the person from that unit.'
                            )
                            ->options(
                                fn (): array =>
                                    ChurchUnit::query()
                                        ->active()
                                        ->ordered()
                                        ->pluck(
                                            'name',
                                            'id'
                                        )
                                        ->all()
                            )
                            ->columns(2)
                            ->gridDirection(
                                'row'
                            )
                            ->bulkToggleable()
                            ->searchable()
                            ->columnSpanFull()
                            ->visible(
                                fn (
                                    ?Member $record
                                ): bool =>
                                    filled($record)
                            ),

                        DatePicker::make(
                            'joined_at'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Date joined'
                            )
                            ->native(false)
                            ->displayFormat(
                                'd M Y'
                            )
                            ->closeOnDateSelection(),

                        Toggle::make(
                            'is_active'
                        )
                            ->label(
                                'Active record'
                            )
                            ->helperText(
                                'Turn this off to archive the member without deleting their record.'
                            )
                            ->default(true),

                        Select::make(
                            'leader_id'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Primary assigned leader'
                            )
                            ->helperText(
                                'The main leader stored on the member record. Individual unit memberships may have different leaders.'
                            )
                            ->options(
                                fn (): array =>
                                    Leader::query()
                                        ->where(
                                            'is_active',
                                            true
                                        )
                                        ->orderBy(
                                            'first_name'
                                        )
                                        ->orderBy(
                                            'last_name'
                                        )
                                        ->get()
                                        ->mapWithKeys(
                                            fn (
                                                Leader $leader
                                            ): array => [
                                                $leader->id =>
                                                    $leader
                                                        ->display_name,
                                            ]
                                        )
                                        ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make(
                            'user_id'
                        )
                            ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                            ->label(
                                'Linked website/LMS account'
                            )
                            ->helperText(
                                'Optional. Linking does not grant CRM or admin access.'
                            )
                            ->options(
                                fn (): array =>
                                    User::query()
                                        ->orderBy(
                                            'name'
                                        )
                                        ->get()
                                        ->mapWithKeys(
                                            fn (
                                                User $user
                                            ): array => [
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


                Section::make('Backend Account')
                    ->description(
                        'Only super administrators can grant or change access to the /hub administration panel.'
                    )
                    ->visible(
                        fn (): bool => BackendAccess::isSuperAdmin()
                    )
                    ->columns(2)
                    ->schema([
                        Toggle::make('backend_access_enabled')
                            ->label('Allow this member to access /hub')
                            ->helperText(
                                'Activates backend access without sending an invitation email. The record email above is used as the /hub login email.'
                            )
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('backend_login_password')
                            ->label('Set / reset backend password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? $state : null)
                            ->helperText(
                                'Optional. Enter a password to set or reset it. Leave blank to keep the current password. For a new account, leaving this blank creates an inaccessible random password until you set one.'
                            )
                            ->columnSpan(1),

                        Placeholder::make('backend_account_status')
                            ->label('Account status')
                            ->content(
                                fn (?Member $record): string =>
                                    self::accountStatus($record)
                            ),

                        Placeholder::make('backend_linked_account')
                            ->label('Backend login email')
                            ->content(
                                fn (?Member $record): string =>
                                    $record?->email ?? 'Enter an email address above'
                            ),

                        Placeholder::make('backend_last_login')
                            ->label('Last login')
                            ->content(
                                fn (?Member $record): string =>
                                    $record?->user?->last_login_at
                                        ? $record->user->last_login_at->format('d M Y, H:i')
                                        : 'Never'
                            ),

                        Placeholder::make('backend_email_notice')
                            ->label('Email invitation')
                            ->content('Not sent — backend accounts are provisioned manually.'),

                        Actions::make([
                            Action::make('deactivate_backend_account')
                                ->label('Deactivate account')
                                ->icon('heroicon-o-no-symbol')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->visible(
                                    fn (?Member $record): bool =>
                                        filled($record?->user_id)
                                        && (bool) $record?->user?->has_backend_access
                                        && ! (bool) $record?->user?->is_admin
                                )
                                ->action(function (?Member $record): void {
                                    if (! $record) {
                                        return;
                                    }

                                    app(MemberBackendAccessService::class)
                                        ->deactivate($record->fresh('user'));

                                    Notification::make()
                                        ->title('Backend account deactivated')
                                        ->success()
                                        ->send();
                                }),
                        ])
                            ->columnSpanFull(),
                    ]),


                Section::make('Backend Permissions')
                    ->description(
                        'Tick the exact areas this member may view or manage. Manage permission is separate from view permission.'
                    )
                    ->visible(
                        fn (): bool => BackendAccess::isSuperAdmin()
                    )
                    ->columns(2)
                    ->schema([
                        ...self::permissionGroups(),
                    ]),

                Section::make(
                    'Communication Preferences'
                )
                    ->visible(fn ($get): bool => $get('record_type') !== Member::TYPE_FUNCTIONAL)
                    ->description(
                        'Control whether this member may receive church communications.'
                    )
                    ->columns(3)
                    ->schema([
                        Toggle::make(
                            'email_consent'
                        )
                            ->label(
                                'Email consent'
                            )
                            ->default(true),

                        Toggle::make(
                            'sms_consent'
                        )
                            ->label(
                                'SMS consent'
                            )
                            ->default(true),

                        Toggle::make(
                            'do_not_contact'
                        )
                            ->label(
                                'Do not contact'
                            )
                            ->helperText(
                                'Overrides both email and SMS consent.'
                            )
                            ->default(false)
                            ->live(),
                    ]),

                Section::make(
                    'Administrative Notes'
                )
                    ->description(
                        'Internal CRM notes. Do not store highly sensitive safeguarding or pastoral information here.'
                    )
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label(
                                'General notes'
                            )
                            ->rows(5)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ]),

                Hidden::make(
                    'legacy_source'
                ),

                Hidden::make(
                    'legacy_id'
                ),

                Hidden::make(
                    'legacy_payload'
                ),
            ]);
    }

    private static function permissionGroups(): array
    {
        return collect(BackendPermissions::GROUPS)
            ->map(function (array $definition, string $group): Section {
                return Section::make($definition['label'])
                    ->compact()
                    ->schema([
                        CheckboxList::make(
                            BackendPermissions::formField($group)
                        )
                            ->label('Permissions')
                            ->options(
                                BackendPermissions::groupOptions($group)
                            )
                            ->columns(1)
                            ->bulkToggleable()
                            ->searchable()
                            ->visible(
                                fn ($get): bool =>
                                    (bool) $get('backend_access_enabled')
                            ),
                    ]);
            })
            ->values()
            ->all();
    }

    private static function accountStatus(?Member $record): string
    {
        $user = $record?->user;

        if (! $user) {
            return 'No account';
        }

        return $user->has_backend_access
            ? 'Active'
            : 'Inactive';
    }

    private static function invitationStatus(?Member $record): string
    {
        $invitation = $record?->user?->latestBackendInvitation();

        if (! $invitation) {
            return 'Not sent';
        }

        if ($invitation->accepted_at) {
            return 'Accepted '.$invitation->accepted_at->format('d M Y, H:i');
        }

        if ($invitation->expires_at?->isPast()) {
            return $invitation->sent_at
                ? 'Expired — last sent '.$invitation->sent_at->format('d M Y, H:i')
                : 'Cancelled or not delivered';
        }

        if (! $invitation->sent_at) {
            return 'Not delivered';
        }

        return 'Sent '.$invitation->sent_at->format('d M Y, H:i')
            .' — expires '.$invitation->expires_at?->format('d M Y, H:i');
    }

}