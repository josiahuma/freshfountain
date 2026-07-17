<?php

namespace App\Filament\Resources\UnitMembershipRequests\Tables;

use App\Models\ChurchUnitMember;
use App\Models\Leader;
use App\Models\UnitMembershipRequest;
use App\Services\Notifications\UnitMembershipNotificationService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

class UnitMembershipRequestsTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->defaultSort(
                'submitted_at',
                'desc'
            )
            ->columns([
                TextColumn::make(
                    'display_name'
                )
                    ->label('Applicant')
                    ->description(
                        fn (
                            UnitMembershipRequest $record
                        ): string =>
                            collect([
                                $record->email,
                                $record->mobile_number,
                                $record->member
                                    ? 'Existing Member'
                                    : 'New Visitor',
                            ])
                                ->filter()
                                ->implode(' • ')
                    )
                    ->searchable([
                        'first_name',
                        'last_name',
                        'email',
                        'mobile_number',
                    ])
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make(
                    'churchUnit.name'
                )
                    ->label(
                        'Requested Unit'
                    )
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                TextColumn::make(
                    'assigned_leader'
                )
                    ->label(
                        'Assigned Leader'
                    )
                    ->state(
                        fn (
                            UnitMembershipRequest $record
                        ): ?string =>
                            $record
                                ->assignedLeader
                                ?->display_name
                    )
                    ->placeholder(
                        'Unassigned'
                    )
                    ->wrap(),

                TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (
                            ?string $state
                        ): string =>
                            UnitMembershipRequest::statusColor(
                                $state
                            )
                    )
                    ->formatStateUsing(
                        fn (
                            ?string $state
                        ): string =>
                            UnitMembershipRequest::statusOptions()[
                                $state
                            ] ?? ucfirst(
                                $state
                                    ?? 'Unknown'
                            )
                    )
                    ->sortable(),

                TextColumn::make(
                    'submission_reference'
                )
                    ->label('Reference')
                    ->copyable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make(
                    'submitted_at'
                )
                    ->label('Submitted')
                    ->since()
                    ->dateTimeTooltip(
                        'd M Y, H:i'
                    )
                    ->sortable(),

                TextColumn::make(
                    'contacted_at'
                )
                    ->label('Contacted')
                    ->dateTime(
                        'd M Y, H:i'
                    )
                    ->placeholder(
                        'Not contacted'
                    )
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make(
                    'reviewed_at'
                )
                    ->label('Reviewed')
                    ->dateTime(
                        'd M Y, H:i'
                    )
                    ->placeholder(
                        'Not reviewed'
                    )
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make(
                    'completed_at'
                )
                    ->label('Completed')
                    ->dateTime(
                        'd M Y, H:i'
                    )
                    ->placeholder(
                        'Not completed'
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
                    ->label(
                        'Church Unit'
                    )
                    ->relationship(
                        'churchUnit',
                        'name'
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make(
                    'assigned_leader_id'
                )
                    ->label(
                        'Assigned Leader'
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

                SelectFilter::make('status')
                    ->options(
                        UnitMembershipRequest::statusOptions()
                    )
                    ->native(false),

                Filter::make('today')
                    ->label(
                        'Submitted Today'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->whereDate(
                                'submitted_at',
                                today()
                            )
                    ),

                Filter::make('unassigned')
                    ->label(
                        'Unassigned'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->whereNull(
                                'assigned_leader_id'
                            )
                    ),

                Filter::make('actionable')
                    ->label(
                        'Action Required'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->actionable()
                    ),

                Filter::make('closed')
                    ->label(
                        'Closed Requests'
                    )
                    ->query(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->closed()
                    ),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),

                EditAction::make()
                    ->visible(
                        fn (
                            UnitMembershipRequest $record
                        ): bool =>
                            $record->canEdit()
                    ),

                Action::make('assign')
                    ->label(
                        'Assign Leader'
                    )
                    ->icon(
                        'heroicon-o-user-plus'
                    )
                    ->color('info')
                    ->visible(
                        fn (
                            UnitMembershipRequest $record
                        ): bool =>
                            $record
                                ->canAssignLeader()
                    )
                    ->form([
                        Select::make(
                            'assigned_leader_id'
                        )
                            ->label('Leader')
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
                            ->native(false)
                            ->required(),
                    ])
                    ->action(
                        function (
                            UnitMembershipRequest $record,
                            array $data,
                            UnitMembershipNotificationService $notifications
                        ): void {
                            if (
                                ! $record
                                    ->canAssignLeader()
                            ) {
                                Notification::make()
                                    ->title(
                                        'This request can no longer be assigned.'
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $leader =
                                Leader::query()
                                    ->whereKey(
                                        $data[
                                            'assigned_leader_id'
                                        ]
                                    )
                                    ->where(
                                        'is_active',
                                        true
                                    )
                                    ->first();

                            if (! $leader) {
                                Notification::make()
                                    ->title(
                                        'The selected leader is unavailable.'
                                    )
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'assigned_leader_id' =>
                                    $leader->id,

                                'assigned_at' =>
                                    now(),

                                'status' =>
                                    UnitMembershipRequest::STATUS_ASSIGNED,
                            ]);

                            $record->refresh();

                            $notifications
                                ->leaderAssigned(
                                    $record
                                );

                            Notification::make()
                                ->title(
                                    'Leader assigned'
                                )
                                ->body(
                                    $leader->display_name
                                    . ' has been assigned and notified.'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('contacted')
                    ->label(
                        'Mark Contacted'
                    )
                    ->icon(
                        'heroicon-o-phone'
                    )
                    ->color('primary')
                    ->visible(
                        fn (
                            UnitMembershipRequest $record
                        ): bool =>
                            $record
                                ->canMarkContacted()
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Mark applicant as contacted?'
                    )
                    ->action(
                        function (
                            UnitMembershipRequest $record
                        ): void {
                            if (
                                ! $record
                                    ->canMarkContacted()
                            ) {
                                Notification::make()
                                    ->title(
                                        'This request cannot be marked as contacted.'
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'status' =>
                                    UnitMembershipRequest::STATUS_CONTACTED,

                                'contacted_at' =>
                                    now(),
                            ]);

                            Notification::make()
                                ->title(
                                    'Marked as contacted'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('approve')
                    ->label('Approve')
                    ->icon(
                        'heroicon-o-check-circle'
                    )
                    ->color('success')
                    ->visible(
                        fn (
                            UnitMembershipRequest $record
                        ): bool =>
                            $record
                                ->canApprove()
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Approve this unit request?'
                    )
                    ->modalDescription(
                        'The applicant will be added to the requested church unit and notification emails will be sent.'
                    )
                    ->action(
                        function (
                            UnitMembershipRequest $record,
                            UnitMembershipNotificationService $notifications
                        ): void {
                            if (
                                ! $record
                                    ->canApprove()
                            ) {
                                Notification::make()
                                    ->title(
                                        'This request cannot be approved from its current status.'
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            if (
                                blank(
                                    $record
                                        ->member_id
                                )
                                || blank(
                                    $record
                                        ->church_unit_id
                                )
                            ) {
                                Notification::make()
                                    ->title(
                                        'Approval could not be completed'
                                    )
                                    ->body(
                                        'The request must be linked to both a member and a church unit.'
                                    )
                                    ->danger()
                                    ->send();

                                return;
                            }

                            try {
                                DB::transaction(
                                    function () use (
                                        $record
                                    ): void {
                                        $record
                                            ->loadMissing(
                                                'member'
                                            );

                                        ChurchUnitMember::query()
                                            ->updateOrCreate(
                                                [
                                                    'member_id' =>
                                                        $record
                                                            ->member_id,

                                                    'church_unit_id' =>
                                                        $record
                                                            ->church_unit_id,
                                                ],
                                                [
                                                    'assigned_leader_id' =>
                                                        $record
                                                            ->assigned_leader_id,

                                                    'status' =>
                                                        'active',

                                                    'source' =>
                                                        'unit_request',

                                                    'joined_at' =>
                                                        today(),

                                                    'left_at' =>
                                                        null,
                                                ]
                                            );

                                        if (
                                            $record
                                                ->member
                                            && blank(
                                                $record
                                                    ->member
                                                    ->church_unit_id
                                            )
                                        ) {
                                            $record
                                                ->member
                                                ->update([
                                                    'church_unit_id' =>
                                                        $record
                                                            ->church_unit_id,

                                                    'leader_id' =>
                                                        $record
                                                            ->assigned_leader_id,
                                                ]);
                                        }

                                        $record->update([
                                            'status' =>
                                                UnitMembershipRequest::STATUS_APPROVED,

                                            'approved_at' =>
                                                now(),

                                            'reviewed_at' =>
                                                now(),

                                            'reviewed_by' =>
                                                auth()->id(),
                                        ]);
                                    }
                                );

                                $record->refresh();

                                $notifications
                                    ->requestApproved(
                                        $record
                                    );

                                Notification::make()
                                    ->title(
                                        'Unit request approved'
                                    )
                                    ->body(
                                        'The member has been added and notification emails have been processed.'
                                    )
                                    ->success()
                                    ->send();
                            } catch (
                                Throwable $exception
                            ) {
                                report(
                                    $exception
                                );

                                Notification::make()
                                    ->title(
                                        'Approval failed'
                                    )
                                    ->body(
                                        'The membership could not be created. Please check the application log.'
                                    )
                                    ->danger()
                                    ->send();
                            }
                        }
                    ),

                Action::make('decline')
                    ->label('Decline')
                    ->icon(
                        'heroicon-o-x-circle'
                    )
                    ->color('danger')
                    ->visible(
                        fn (
                            UnitMembershipRequest $record
                        ): bool =>
                            $record
                                ->canDecline()
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Decline this request?'
                    )
                    ->modalDescription(
                        'The applicant and unit contact email will be notified.'
                    )
                    ->action(
                        function (
                            UnitMembershipRequest $record,
                            UnitMembershipNotificationService $notifications
                        ): void {
                            if (
                                ! $record
                                    ->canDecline()
                            ) {
                                Notification::make()
                                    ->title(
                                        'This request cannot be declined from its current status.'
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'status' =>
                                    UnitMembershipRequest::STATUS_DECLINED,

                                'declined_at' =>
                                    now(),

                                'reviewed_at' =>
                                    now(),

                                'reviewed_by' =>
                                    auth()->id(),
                            ]);

                            $record->refresh();

                            $notifications
                                ->requestDeclined(
                                    $record
                                );

                            Notification::make()
                                ->title(
                                    'Request declined'
                                )
                                ->danger()
                                ->send();
                        }
                    ),

                Action::make('complete')
                    ->label('Complete')
                    ->icon(
                        'heroicon-o-trophy'
                    )
                    ->color('success')
                    ->visible(
                        fn (
                            UnitMembershipRequest $record
                        ): bool =>
                            $record
                                ->canComplete()
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Complete this request?'
                    )
                    ->modalDescription(
                        'The applicant and assigned leader will be notified.'
                    )
                    ->action(
                        function (
                            UnitMembershipRequest $record,
                            UnitMembershipNotificationService $notifications
                        ): void {
                            if (
                                ! $record
                                    ->canComplete()
                            ) {
                                Notification::make()
                                    ->title(
                                        'This request cannot be completed.'
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'status' =>
                                    UnitMembershipRequest::STATUS_COMPLETED,

                                'completed_at' =>
                                    now(),
                            ]);

                            $record->refresh();

                            $notifications
                                ->requestCompleted(
                                    $record
                                );

                            Notification::make()
                                ->title(
                                    'Request completed'
                                )
                                ->success()
                                ->send();
                        }
                    ),
            ])
            ->emptyStateHeading(
                'No unit membership requests'
            )
            ->emptyStateDescription(
                'New requests submitted from the public church-unit pages will appear here.'
            )
            ->emptyStateIcon(
                'heroicon-o-user-group'
            );
    }
}