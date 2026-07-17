<?php

namespace App\Filament\Resources\Members\Tables;

use App\Models\ChurchUnit;
use App\Models\Leader;
use App\Models\Member;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('first_name')
            ->columns([
                TextColumn::make('display_name')
                    ->label('Member')
                    ->state(
                        fn (
                            Member $record
                        ): string =>
                            $record->display_name
                    )
                    ->description(
                        fn (
                            Member $record
                        ): ?string =>
                            $record->email
                            ?: $record->mobile_number
                    )
                    ->searchable([
                        'first_name',
                        'middle_name',
                        'last_name',
                        'email',
                        'mobile_number',
                    ])
                    ->sortable(
                        query: fn (
                            Builder $query,
                            string $direction
                        ): Builder =>
                            $query
                                ->orderBy(
                                    'first_name',
                                    $direction
                                )
                                ->orderBy(
                                    'last_name',
                                    $direction
                                )
                    )
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make(
                    'churchUnit.name'
                )
                    ->label('Primary Unit')
                    ->placeholder(
                        'Not assigned'
                    )
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make(
                    'churchUnits.name'
                )
                    ->label('All Units')
                    ->badge()
                    ->separator(',')
                    ->placeholder(
                        'No memberships'
                    )
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->wrap()
                    ->toggleable(),

                TextColumn::make(
                    'leader.display_name'
                )
                    ->label('Primary Leader')
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
                    )
                    ->searchable([
                        'first_name',
                        'middle_name',
                        'last_name',
                    ])
                    ->wrap()
                    ->toggleable(),

                TextColumn::make(
                    'unit_leader_assignments'
                )
                    ->label('Unit Leaders')
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
                                            . ': '
                                            . (
                                                $leader
                                                    ?->display_name
                                                ?? 'Unassigned'
                                            );
                                    }
                                )
                                ->values()
                                ->all();
                        }
                    )
                    ->bulleted()
                    ->placeholder(
                        'No unit assignments'
                    )
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->wrap()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make(
                    'membership_status'
                )
                    ->label('Status')
                    ->formatStateUsing(
                        fn (
                            ?string $state
                        ): string =>
                            Member::statusOptions()[
                                $state
                            ] ?? ucfirst(
                                $state ?? 'Unknown'
                            )
                    )
                    ->badge()
                    ->color(
                        fn (
                            ?string $state
                        ): string =>
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
                    )
                    ->sortable(),

                TextColumn::make(
                    'mobile_number'
                )
                    ->label('Mobile')
                    ->placeholder(
                        'Not provided'
                    )
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->placeholder(
                        'Not provided'
                    )
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make(
                    'date_of_birth'
                )
                    ->label('Birthday')
                    ->date('d M')
                    ->placeholder(
                        'Not provided'
                    )
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(
                    'anniversary_date'
                )
                    ->label('Anniversary')
                    ->date('d M')
                    ->placeholder(
                        'Not provided'
                    )
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('joined_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->placeholder(
                        'Not provided'
                    )
                    ->sortable()
                    ->toggleable(),

                IconColumn::make(
                    'email_consent'
                )
                    ->label('Email')
                    ->boolean()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                IconColumn::make(
                    'sms_consent'
                )
                    ->label('SMS')
                    ->boolean()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                IconColumn::make(
                    'do_not_contact'
                )
                    ->label('No Contact')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->dateTimeTooltip(
                        'd M Y, H:i'
                    )
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                SelectFilter::make(
                    'church_unit_id'
                )
                    ->label('Primary Unit')
                    ->options(
                        fn (): array =>
                            ChurchUnit::query()
                                ->ordered()
                                ->pluck(
                                    'name',
                                    'id'
                                )
                                ->all()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make(
                    'all_unit_membership'
                )
                    ->label(
                        'Any Unit Membership'
                    )
                    ->options(
                        fn (): array =>
                            ChurchUnit::query()
                                ->ordered()
                                ->pluck(
                                    'name',
                                    'id'
                                )
                                ->all()
                    )
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {
                            $unitId =
                                $data['value']
                                ?? null;

                            return $query->when(
                                filled($unitId),
                                fn (
                                    Builder $memberQuery
                                ): Builder =>
                                    $memberQuery
                                        ->whereHas(
                                            'churchUnits',
                                            fn (
                                                Builder $unitQuery
                                            ): Builder =>
                                                $unitQuery->whereKey(
                                                    $unitId
                                                )
                                        )
                            );
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make(
                    'leader_id'
                )
                    ->label(
                        'Primary Leader'
                    )
                    ->options(
                        fn (): array =>
                            Leader::query()
                                ->active()
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

                SelectFilter::make(
                    'assigned_unit_leader'
                )
                    ->label(
                        'Any Assigned Unit Leader'
                    )
                    ->options(
                        fn (): array =>
                            Leader::query()
                                ->active()
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
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {
                            $leaderId =
                                $data['value']
                                ?? null;

                            return $query->when(
                                filled($leaderId),
                                fn (
                                    Builder $memberQuery
                                ): Builder =>
                                    $memberQuery
                                        ->whereHas(
                                            'churchUnits',
                                            fn (
                                                Builder $unitQuery
                                            ): Builder =>
                                                $unitQuery->wherePivot(
                                                    'assigned_leader_id',
                                                    $leaderId
                                                )
                                        )
                            );
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                Filter::make(
                    'not_assigned_to_leader'
                )
                    ->label(
                        'No Primary Leader'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->whereNull(
                                'leader_id'
                            )
                    ),

                SelectFilter::make(
                    'membership_status'
                )
                    ->label(
                        'Membership Status'
                    )
                    ->options(
                        Member::statusOptions()
                    )
                    ->native(false),

                SelectFilter::make('gender')
                    ->options(
                        Member::genderOptions()
                    )
                    ->native(false),

                TernaryFilter::make(
                    'is_active'
                )
                    ->label(
                        'Active Records'
                    ),

                TernaryFilter::make(
                    'do_not_contact'
                )
                    ->label(
                        'Do Not Contact'
                    ),

                Filter::make(
                    'birthdays_this_month'
                )
                    ->label(
                        'Birthdays This Month'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query
                                ->whereNotNull(
                                    'date_of_birth'
                                )
                                ->whereMonth(
                                    'date_of_birth',
                                    now()->month
                                )
                    ),

                Filter::make(
                    'anniversaries_this_month'
                )
                    ->label(
                        'Anniversaries This Month'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query
                                ->whereNotNull(
                                    'anniversary_date'
                                )
                                ->whereMonth(
                                    'anniversary_date',
                                    now()->month
                                )
                    ),

                Filter::make(
                    'missing_contact_details'
                )
                    ->label(
                        'Missing Contact Details'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->where(
                                function (
                                    Builder $contact
                                ): void {
                                    $contact
                                        ->whereNull(
                                            'email'
                                        )
                                        ->whereNull(
                                            'mobile_number'
                                        );
                                }
                            )
                    ),

                Filter::make(
                    'not_assigned_to_unit'
                )
                    ->label(
                        'No Primary Unit'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->whereNull(
                                'church_unit_id'
                            )
                    ),

                Filter::make(
                    'no_unit_memberships'
                )
                    ->label(
                        'No Unit Memberships'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->whereDoesntHave(
                                'churchUnits'
                            )
                    ),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),

                EditAction::make(),

                Action::make('archive')
                    ->label('Archive')
                    ->icon(
                        Heroicon::OutlinedArchiveBox
                    )
                    ->color('warning')
                    ->visible(
                        fn (
                            Member $record
                        ): bool =>
                            $record->is_active
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Archive this member?'
                    )
                    ->modalDescription(
                        'The member will remain in the CRM but will be marked as inactive.'
                    )
                    ->action(
                        function (
                            Member $record
                        ): void {
                            $record->update([
                                'is_active' =>
                                    false,

                                'membership_status' =>
                                    Member::STATUS_INACTIVE,
                            ]);

                            Notification::make()
                                ->title(
                                    'Member archived'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('restore')
                    ->label('Restore')
                    ->icon(
                        Heroicon::OutlinedArrowPath
                    )
                    ->color('success')
                    ->visible(
                        fn (
                            Member $record
                        ): bool =>
                            ! $record->is_active
                    )
                    ->requiresConfirmation()
                    ->action(
                        function (
                            Member $record
                        ): void {
                            $record->update([
                                'is_active' =>
                                    true,

                                'membership_status' =>
                                    Member::STATUS_ACTIVE,
                            ]);

                            Notification::make()
                                ->title(
                                    'Member restored'
                                )
                                ->success()
                                ->send();
                        }
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make(
                        'archive'
                    )
                        ->label(
                            'Archive Selected'
                        )
                        ->icon(
                            Heroicon::OutlinedArchiveBox
                        )
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(
                            function (
                                Collection $records
                            ): void {
                                Member::query()
                                    ->whereKey(
                                        $records
                                            ->modelKeys()
                                    )
                                    ->update([
                                        'is_active' =>
                                            false,

                                        'membership_status' =>
                                            Member::STATUS_INACTIVE,
                                    ]);

                                Notification::make()
                                    ->title(
                                        'Selected members archived'
                                    )
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make(
                        'restore'
                    )
                        ->label(
                            'Restore Selected'
                        )
                        ->icon(
                            Heroicon::OutlinedArrowPath
                        )
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(
                            function (
                                Collection $records
                            ): void {
                                Member::query()
                                    ->whereKey(
                                        $records
                                            ->modelKeys()
                                    )
                                    ->update([
                                        'is_active' =>
                                            true,

                                        'membership_status' =>
                                            Member::STATUS_ACTIVE,
                                    ]);

                                Notification::make()
                                    ->title(
                                        'Selected members restored'
                                    )
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading(
                'No members yet'
            )
            ->emptyStateDescription(
                'Create a member manually or import members from the legacy OviBase CRM.'
            )
            ->emptyStateIcon(
                Heroicon::OutlinedUsers
            );
    }
}