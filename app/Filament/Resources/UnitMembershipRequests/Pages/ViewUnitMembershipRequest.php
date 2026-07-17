<?php

namespace App\Filament\Resources\UnitMembershipRequests\Pages;

use App\Filament\Resources\UnitMembershipRequests\UnitMembershipRequestResource;
use App\Models\ChurchUnitMember;
use App\Models\Leader;
use App\Models\UnitMembershipRequest;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitMembershipRequest extends ViewRecord
{
    protected static string $resource =
        UnitMembershipRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [

            EditAction::make(),

            Action::make('assign')
                ->label('Assign Leader')
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->visible(fn () => blank($this->record->assigned_leader_id))
                ->form([
                    Select::make('assigned_leader_id')
                        ->label('Leader')
                        ->options(
                            fn (): array =>
                                Leader::query()
                                    ->where('is_active', true)
                                    ->orderBy('first_name')
                                    ->orderBy('last_name')
                                    ->get()
                                    ->mapWithKeys(
                                        fn (Leader $leader): array => [
                                            $leader->id =>
                                                $leader->display_name,
                                        ]
                                    )
                                    ->all()
                        )
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {

                    $this->record->update([
                        'assigned_leader_id' => $data['assigned_leader_id'],
                        'assigned_at' => now(),
                        'status' => UnitMembershipRequest::STATUS_ASSIGNED,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Leader assigned')
                        ->send();
                }),

            Action::make('contacted')
                ->label('Mark Contacted')
                ->icon('heroicon-o-phone')
                ->color('primary')
                ->visible(fn () => in_array(
                    $this->record->status,
                    [
                        UnitMembershipRequest::STATUS_PENDING,
                        UnitMembershipRequest::STATUS_ASSIGNED,
                    ]
                ))
                ->requiresConfirmation()
                ->action(function (): void {

                    $this->record->update([
                        'status' => UnitMembershipRequest::STATUS_CONTACTED,
                        'contacted_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Marked as contacted')
                        ->send();
                }),

            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => ! in_array(
                    $this->record->status,
                    [
                        UnitMembershipRequest::STATUS_APPROVED,
                        UnitMembershipRequest::STATUS_COMPLETED,
                    ]
                ))
                ->requiresConfirmation()
                ->action(function (): void {

                    if ($this->record->member) {

                        ChurchUnitMember::firstOrCreate(

                            [
                                'member_id' =>
                                    $this->record->member_id,

                                'church_unit_id' =>
                                    $this->record->church_unit_id,
                            ],

                            [
                                'assigned_leader_id' =>
                                    $this->record->assigned_leader_id,

                                'status' => 'active',

                                'source' => 'unit_request',

                                'joined_at' => today(),
                            ]

                        );

                        if (blank(
                            $this->record->member->church_unit_id
                        )) {

                            $this->record->member->update([
                                'church_unit_id' =>
                                    $this->record->church_unit_id,

                                'leader_id' =>
                                    $this->record->assigned_leader_id,
                            ]);
                        }
                    }

                    $this->record->update([
                        'status' => UnitMembershipRequest::STATUS_APPROVED,
                        'approved_at' => now(),
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Request approved')
                        ->send();
                }),

            Action::make('complete')
                ->label('Complete')
                ->icon('heroicon-o-trophy')
                ->color('success')
                ->visible(fn () =>
                    $this->record->status === UnitMembershipRequest::STATUS_APPROVED
                )
                ->requiresConfirmation()
                ->action(function (): void {

                    $this->record->update([
                        'status' => UnitMembershipRequest::STATUS_COMPLETED,
                        'completed_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Request completed')
                        ->send();
                }),

            Action::make('decline')
                ->label('Decline')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () =>
                    $this->record->status !== UnitMembershipRequest::STATUS_DECLINED
                )
                ->requiresConfirmation()
                ->action(function (): void {

                    $this->record->update([
                        'status' => UnitMembershipRequest::STATUS_DECLINED,
                        'declined_at' => now(),
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->danger()
                        ->title('Request declined')
                        ->send();
                }),

            DeleteAction::make(),

        ];
    }
}