<?php

namespace App\Filament\Resources\CourseEnrollments\Pages;

use App\Filament\Resources\CourseEnrollments\CourseEnrollmentResource;
use App\Models\CourseEnrollment;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCourseEnrollments extends ListRecords
{
    protected static string $resource =
        CourseEnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(
                    CourseEnrollment::query()
                        ->count()
                ),

            'active' => Tab::make('Active')
                ->modifyQueryUsing(
                    fn (
                        Builder $query
                    ): Builder =>
                        $query->where(
                            'status',
                            CourseEnrollment::STATUS_ACTIVE
                        )
                )
                ->badge(
                    CourseEnrollment::query()
                        ->where(
                            'status',
                            CourseEnrollment::STATUS_ACTIVE
                        )
                        ->count()
                )
                ->badgeColor('info'),

            'completed' => Tab::make(
                'Completed'
            )
                ->modifyQueryUsing(
                    fn (
                        Builder $query
                    ): Builder =>
                        $query->where(
                            'status',
                            CourseEnrollment::STATUS_COMPLETED
                        )
                )
                ->badge(
                    CourseEnrollment::query()
                        ->where(
                            'status',
                            CourseEnrollment::STATUS_COMPLETED
                        )
                        ->count()
                )
                ->badgeColor('success'),

            'paused' => Tab::make('Paused')
                ->modifyQueryUsing(
                    fn (
                        Builder $query
                    ): Builder =>
                        $query->where(
                            'status',
                            CourseEnrollment::STATUS_PAUSED
                        )
                )
                ->badge(
                    CourseEnrollment::query()
                        ->where(
                            'status',
                            CourseEnrollment::STATUS_PAUSED
                        )
                        ->count()
                )
                ->badgeColor('warning'),

            'inactive' => Tab::make(
                'Needs Attention'
            )
                ->modifyQueryUsing(
                    fn (
                        Builder $query
                    ): Builder =>
                        $query
                            ->where(
                                'status',
                                CourseEnrollment::STATUS_ACTIVE
                            )
                            ->where(
                                'last_activity_at',
                                '<=',
                                now()->subDays(7)
                            )
                )
                ->badge(
                    CourseEnrollment::query()
                        ->where(
                            'status',
                            CourseEnrollment::STATUS_ACTIVE
                        )
                        ->where(
                            'last_activity_at',
                            '<=',
                            now()->subDays(7)
                        )
                        ->count()
                )
                ->badgeColor('danger'),
        ];
    }
}