<?php

namespace App\Filament\Resources\UnitMembershipRequests\Pages;

use App\Filament\Resources\UnitMembershipRequests\UnitMembershipRequestResource;
use App\Models\UnitMembershipRequest;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUnitMembershipRequests extends ListRecords
{
    protected static string $resource =
        UnitMembershipRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [

            'all' => Tab::make('All')
                ->badge(
                    UnitMembershipRequest::query()
                        ->count()
                ),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder =>
                        $query->where(
                            'status',
                            UnitMembershipRequest::STATUS_PENDING
                        )
                )
                ->badge(
                    UnitMembershipRequest::query()
                        ->where(
                            'status',
                            UnitMembershipRequest::STATUS_PENDING
                        )
                        ->count()
                )
                ->badgeColor('warning'),

            'assigned' => Tab::make('Assigned')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder =>
                        $query->where(
                            'status',
                            UnitMembershipRequest::STATUS_ASSIGNED
                        )
                )
                ->badge(
                    UnitMembershipRequest::query()
                        ->where(
                            'status',
                            UnitMembershipRequest::STATUS_ASSIGNED
                        )
                        ->count()
                )
                ->badgeColor('info'),

            'contacted' => Tab::make('Contacted')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder =>
                        $query->where(
                            'status',
                            UnitMembershipRequest::STATUS_CONTACTED
                        )
                )
                ->badge(
                    UnitMembershipRequest::query()
                        ->where(
                            'status',
                            UnitMembershipRequest::STATUS_CONTACTED
                        )
                        ->count()
                )
                ->badgeColor('primary'),

            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder =>
                        $query->where(
                            'status',
                            UnitMembershipRequest::STATUS_APPROVED
                        )
                )
                ->badge(
                    UnitMembershipRequest::query()
                        ->where(
                            'status',
                            UnitMembershipRequest::STATUS_APPROVED
                        )
                        ->count()
                )
                ->badgeColor('success'),

            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder =>
                        $query->where(
                            'status',
                            UnitMembershipRequest::STATUS_COMPLETED
                        )
                )
                ->badge(
                    UnitMembershipRequest::query()
                        ->where(
                            'status',
                            UnitMembershipRequest::STATUS_COMPLETED
                        )
                        ->count()
                )
                ->badgeColor('success'),

            'declined' => Tab::make('Declined')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder =>
                        $query->where(
                            'status',
                            UnitMembershipRequest::STATUS_DECLINED
                        )
                )
                ->badge(
                    UnitMembershipRequest::query()
                        ->where(
                            'status',
                            UnitMembershipRequest::STATUS_DECLINED
                        )
                        ->count()
                )
                ->badgeColor('danger'),

            'withdrawn' => Tab::make('Withdrawn')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder =>
                        $query->where(
                            'status',
                            UnitMembershipRequest::STATUS_WITHDRAWN
                        )
                )
                ->badge(
                    UnitMembershipRequest::query()
                        ->where(
                            'status',
                            UnitMembershipRequest::STATUS_WITHDRAWN
                        )
                        ->count()
                )
                ->badgeColor('gray'),
        ];
    }
}