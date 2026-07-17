<?php

namespace App\Filament\Resources\UnitMembershipRequests\Tables;

use App\Models\ChurchUnit;
use App\Models\Leader;
use App\Models\UnitMembershipRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UnitMembershipRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')

            ->columns([

                TextColumn::make('display_name')
                    ->label('Applicant')
                    ->description(fn (UnitMembershipRequest $record) => collect([
                        $record->email,
                        $record->mobile_number,
                        $record->member
                            ? 'Existing Member'
                            : 'New Visitor',
                    ])->filter()->implode(' • '))
                    ->searchable([
                        'first_name',
                        'last_name',
                        'email',
                        'mobile_number',
                    ])
                    ->sortable(),

                TextColumn::make('churchUnit.name')
                    ->label('Requested Unit')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('assigned_leader')
                    ->label('Assigned Leader')
                    ->state(
                        fn (UnitMembershipRequest $record): ?string =>
                            $record->assignedLeader?->display_name
                    )
                    ->placeholder('Unassigned')
                    ->wrap(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        UnitMembershipRequest::STATUS_PENDING => 'warning',
                        UnitMembershipRequest::STATUS_ASSIGNED => 'info',
                        UnitMembershipRequest::STATUS_CONTACTED => 'primary',
                        UnitMembershipRequest::STATUS_APPROVED => 'success',
                        UnitMembershipRequest::STATUS_COMPLETED => 'success',
                        UnitMembershipRequest::STATUS_DECLINED => 'danger',
                        UnitMembershipRequest::STATUS_WITHDRAWN => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                TextColumn::make('submission_reference')
                    ->label('Reference')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])

            ->filters([

                SelectFilter::make('church_unit_id')
                    ->label('Church Unit')
                    ->relationship('churchUnit', 'name'),

                SelectFilter::make('assigned_leader_id')
                    ->label('Assigned Leader')
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
                    ->preload()
                    ->native(false),

                SelectFilter::make('status')
                    ->options(UnitMembershipRequest::statusOptions()),

                Filter::make('today')
                    ->query(fn ($query) => $query->whereDate(
                        'submitted_at',
                        today()
                    )),

                Filter::make('unassigned')
                    ->query(fn ($query) => $query->whereNull(
                        'assigned_leader_id'
                    )),
            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

                Action::make('assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (UnitMembershipRequest $record) =>
                        blank($record->assigned_leader_id)
                    )
                    ->form([
                        Select::make('assigned_leader_id')
                            ->label('Leader')
                            ->options(
                                Leader::query()
                                    ->where('is_active', true)
                                    ->orderBy('first_name')
                                    ->get()
                                    ->pluck('display_name', 'id')
                            )
                            ->required(),
                    ])
                    ->action(function (
                        UnitMembershipRequest $record,
                        array $data
                    ) {
                        $record->update([
                            'assigned_leader_id' => $data['assigned_leader_id'],
                            'assigned_at' => now(),
                            'status' => UnitMembershipRequest::STATUS_ASSIGNED,
                        ]);
                    }),

                Action::make('contacted')
                    ->icon('heroicon-o-phone')
                    ->color('primary')
                    ->visible(fn (UnitMembershipRequest $record) =>
                        in_array($record->status, [
                            UnitMembershipRequest::STATUS_PENDING,
                            UnitMembershipRequest::STATUS_ASSIGNED,
                        ])
                    )
                    ->requiresConfirmation()
                    ->action(fn (UnitMembershipRequest $record) =>
                        $record->update([
                            'status' => UnitMembershipRequest::STATUS_CONTACTED,
                            'contacted_at' => now(),
                        ])
                    ),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn (UnitMembershipRequest $record) =>
                            $record->status !== UnitMembershipRequest::STATUS_APPROVED
                            && $record->status !== UnitMembershipRequest::STATUS_COMPLETED
                    )
                    ->action(function (UnitMembershipRequest $record): void {

                        if ($record->member) {

                            \App\Models\ChurchUnitMember::firstOrCreate(

                                [
                                    'member_id' => $record->member_id,
                                    'church_unit_id' => $record->church_unit_id,
                                ],

                                [
                                    'assigned_leader_id' => $record->assigned_leader_id,

                                    'status' => 'active',

                                    'source' => 'unit_request',

                                    'joined_at' => today(),
                                ]
                            );

                            /*
                            |-----------------------------------------
                            | Update primary unit if empty
                            |-----------------------------------------
                            */

                            if (blank($record->member->church_unit_id)) {

                                $record->member->update([
                                    'church_unit_id' => $record->church_unit_id,
                                    'leader_id' => $record->assigned_leader_id,
                                ]);
                            }
                        }

                        $record->update([

                            'status' => UnitMembershipRequest::STATUS_APPROVED,

                            'approved_at' => now(),

                            'reviewed_at' => now(),

                            'reviewed_by' => auth()->id(),

                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Unit request approved')
                            ->success()
                            ->send();

                    }),

                Action::make('decline')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (UnitMembershipRequest $record) =>
                        $record->status !== UnitMembershipRequest::STATUS_DECLINED
                    )
                    ->action(fn (UnitMembershipRequest $record) =>
                        $record->update([
                            'status' => UnitMembershipRequest::STATUS_DECLINED,
                            'declined_at' => now(),
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ])
                    ),

                Action::make('complete')
                    ->icon('heroicon-o-trophy')
                    ->color('success')
                    ->visible(fn (UnitMembershipRequest $record) =>
                        $record->status === UnitMembershipRequest::STATUS_APPROVED
                    )
                    ->requiresConfirmation()
                    ->action(fn (UnitMembershipRequest $record) =>
                        $record->update([
                            'status' => UnitMembershipRequest::STATUS_COMPLETED,
                            'completed_at' => now(),
                        ])
                    ),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}