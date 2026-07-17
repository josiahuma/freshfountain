<?php

namespace App\Filament\Resources\UnitMembershipRequests\Pages;

use App\Filament\Resources\UnitMembershipRequests\UnitMembershipRequestResource;
use App\Models\ChurchUnitMember;
use App\Models\Leader;
use App\Models\UnitMembershipRequest;
use App\Services\Notifications\UnitMembershipNotificationService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Throwable;

class ViewUnitMembershipRequest extends ViewRecord
{
    protected static string $resource =
        UnitMembershipRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(
                    fn (): bool =>
                        $this->record
                            ->canEdit()
                ),

            Action::make('assign')
                ->label('Assign Leader')
                ->icon(
                    'heroicon-o-user-plus'
                )
                ->color('info')
                ->visible(
                    fn (): bool =>
                        $this->record
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
                        array $data,
                        UnitMembershipNotificationService $notifications
                    ): void {
                        if (
                            ! $this->record
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

                        $leader = Leader::query()
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

                        $this->record->update([
                            'assigned_leader_id' =>
                                $leader->id,

                            'assigned_at' =>
                                now(),

                            'status' =>
                                UnitMembershipRequest::STATUS_ASSIGNED,
                        ]);

                        $this->record->refresh();

                        $notifications
                            ->leaderAssigned(
                                $this->record
                            );

                        Notification::make()
                            ->success()
                            ->title(
                                'Leader assigned'
                            )
                            ->body(
                                $leader->display_name
                                . ' has been assigned and notified.'
                            )
                            ->send();
                    }
                ),

            Action::make('contacted')
                ->label('Mark Contacted')
                ->icon(
                    'heroicon-o-phone'
                )
                ->color('primary')
                ->visible(
                    fn (): bool =>
                        $this->record
                            ->canMarkContacted()
                )
                ->requiresConfirmation()
                ->modalHeading(
                    'Mark applicant as contacted?'
                )
                ->modalDescription(
                    'This confirms that the assigned leader has contacted the applicant.'
                )
                ->action(
                    function (): void {
                        if (
                            ! $this->record
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

                        $this->record->update([
                            'status' =>
                                UnitMembershipRequest::STATUS_CONTACTED,

                            'contacted_at' =>
                                now(),
                        ]);

                        $this->record->refresh();

                        Notification::make()
                            ->success()
                            ->title(
                                'Marked as contacted'
                            )
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
                    fn (): bool =>
                        $this->record
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
                        UnitMembershipNotificationService $notifications
                    ): void {
                        if (
                            ! $this->record
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
                                $this->record
                                    ->member_id
                            )
                            || blank(
                                $this->record
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
                                function (): void {
                                    $this->record
                                        ->loadMissing(
                                            'member'
                                        );

                                    ChurchUnitMember::query()
                                        ->updateOrCreate(
                                            [
                                                'member_id' =>
                                                    $this
                                                        ->record
                                                        ->member_id,

                                                'church_unit_id' =>
                                                    $this
                                                        ->record
                                                        ->church_unit_id,
                                            ],
                                            [
                                                'assigned_leader_id' =>
                                                    $this
                                                        ->record
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
                                        $this->record
                                            ->member
                                        && blank(
                                            $this
                                                ->record
                                                ->member
                                                ->church_unit_id
                                        )
                                    ) {
                                        $this->record
                                            ->member
                                            ->update([
                                                'church_unit_id' =>
                                                    $this
                                                        ->record
                                                        ->church_unit_id,

                                                'leader_id' =>
                                                    $this
                                                        ->record
                                                        ->assigned_leader_id,
                                            ]);
                                    }

                                    $this->record
                                        ->update([
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

                            $this->record->refresh();

                            $notifications
                                ->requestApproved(
                                    $this->record
                                );

                            Notification::make()
                                ->success()
                                ->title(
                                    'Request approved'
                                )
                                ->body(
                                    'The member has been added to the unit and notification emails have been processed.'
                                )
                                ->send();
                        } catch (
                            Throwable $exception
                        ) {
                            report($exception);

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

            Action::make('complete')
                ->label('Complete')
                ->icon(
                    'heroicon-o-trophy'
                )
                ->color('success')
                ->visible(
                    fn (): bool =>
                        $this->record
                            ->canComplete()
                )
                ->requiresConfirmation()
                ->modalHeading(
                    'Complete this request?'
                )
                ->modalDescription(
                    'Completed requests become read-only audit records. The applicant and assigned leader will be notified.'
                )
                ->action(
                    function (
                        UnitMembershipNotificationService $notifications
                    ): void {
                        if (
                            ! $this->record
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

                        $this->record->update([
                            'status' =>
                                UnitMembershipRequest::STATUS_COMPLETED,

                            'completed_at' =>
                                now(),
                        ]);

                        $this->record->refresh();

                        $notifications
                            ->requestCompleted(
                                $this->record
                            );

                        Notification::make()
                            ->success()
                            ->title(
                                'Request completed'
                            )
                            ->send();
                    }
                ),

            Action::make('decline')
                ->label('Decline')
                ->icon(
                    'heroicon-o-x-circle'
                )
                ->color('danger')
                ->visible(
                    fn (): bool =>
                        $this->record
                            ->canDecline()
                )
                ->requiresConfirmation()
                ->modalHeading(
                    'Decline this request?'
                )
                ->modalDescription(
                    'The applicant will receive a polite update and the unit contact email will be notified.'
                )
                ->action(
                    function (
                        UnitMembershipNotificationService $notifications
                    ): void {
                        if (
                            ! $this->record
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

                        $this->record->update([
                            'status' =>
                                UnitMembershipRequest::STATUS_DECLINED,

                            'declined_at' =>
                                now(),

                            'reviewed_at' =>
                                now(),

                            'reviewed_by' =>
                                auth()->id(),
                        ]);

                        $this->record->refresh();

                        $notifications
                            ->requestDeclined(
                                $this->record
                            );

                        Notification::make()
                            ->danger()
                            ->title(
                                'Request declined'
                            )
                            ->send();
                    }
                ),
        ];
    }
}