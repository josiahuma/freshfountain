<?php

namespace App\Filament\Resources\CourseEnrollments\Tables;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseEnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(
                'last_activity_at',
                'desc'
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student')
                    ->description(
                        fn (CourseEnrollment $record): ?string =>
                            $record->user?->email
                    )
                    ->searchable([
                        'name',
                        'email',
                    ])
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->suffix('%')
                    ->badge()
                    ->color(
                        fn (int|string|null $state): string =>
                            match (true) {
                                (int) $state >= 100 =>
                                    'success',

                                (int) $state >= 50 =>
                                    'info',

                                (int) $state > 0 =>
                                    'warning',

                                default => 'gray',
                            }
                    )
                    ->sortable(),

                TextColumn::make('lesson_progress')
                    ->label('Lessons')
                    ->state(
                        fn (
                            CourseEnrollment $record
                        ): string =>
                            $record->completedLessonsCount()
                            . ' / '
                            . $record->totalPublishedLessonsCount()
                    )
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            ucfirst($state ?? 'Unknown')
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                            match ($state) {
                                CourseEnrollment::STATUS_COMPLETED =>
                                    'success',

                                CourseEnrollment::STATUS_ACTIVE =>
                                    'info',

                                CourseEnrollment::STATUS_PAUSED =>
                                    'warning',

                                CourseEnrollment::STATUS_CANCELLED =>
                                    'danger',

                                default => 'gray',
                            }
                    )
                    ->sortable(),

                TextColumn::make('quiz_attempts_count')
                    ->label('Quiz Attempts')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make(
                    'passed_quiz_attempts_count'
                )
                    ->label('Passed')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make(
                    'quiz_attempts_avg_percentage'
                )
                    ->label('Average Score')
                    ->formatStateUsing(
                        fn (
                            float|int|string|null $state
                        ): string =>
                            $state === null
                                ? 'No attempts'
                                : round((float) $state) . '%'
                    )
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('lastLesson.title')
                    ->label('Last Lesson')
                    ->placeholder('Not started')
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('last_activity_at')
                    ->label('Last Activity')
                    ->since()
                    ->dateTimeTooltip(
                        'd M Y, H:i'
                    )
                    ->placeholder('No activity')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('enrolled_at')
                    ->label('Enrolled')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->options(
                        fn (): array =>
                            Course::query()
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->all()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(
                        CourseEnrollment::statusOptions()
                    )
                    ->native(false),

                Filter::make('active_today')
                    ->label('Active today')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query->whereDate(
                                'last_activity_at',
                                today()
                            )
                    ),

                Filter::make('has_quiz_attempts')
                    ->label('Has quiz attempts')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query->whereHas(
                                'quizAttempts'
                            )
                    ),

                Filter::make('has_failed_quiz_attempts')
                    ->label('Has failed quiz attempts')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query->whereHas(
                                'quizAttempts',
                                fn (
                                    Builder $attempts
                                ): Builder =>
                                    $attempts->where(
                                        'passed',
                                        false
                                    )
                            )
                    ),

                Filter::make('inactive_7_days')
                    ->label(
                        'Inactive for 7+ days'
                    )
                    ->query(
                        fn (Builder $query): Builder =>
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
                    ),

                Filter::make('inactive_14_days')
                    ->label(
                        'Inactive for 14+ days'
                    )
                    ->query(
                        fn (Builder $query): Builder =>
                            $query
                                ->where(
                                    'status',
                                    CourseEnrollment::STATUS_ACTIVE
                                )
                                ->where(
                                    'last_activity_at',
                                    '<=',
                                    now()->subDays(14)
                                )
                    ),

                Filter::make('not_started')
                    ->label('Not started')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query
                                ->whereNull('started_at')
                                ->where(
                                    'progress_percentage',
                                    0
                                )
                    ),

                Filter::make('in_progress')
                    ->label('In progress')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query
                                ->where(
                                    'status',
                                    CourseEnrollment::STATUS_ACTIVE
                                )
                                ->where(
                                    'progress_percentage',
                                    '>',
                                    0
                                )
                                ->where(
                                    'progress_percentage',
                                    '<',
                                    100
                                )
                    ),

                Filter::make('completed')
                    ->label('Completed courses')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query->where(
                                'status',
                                CourseEnrollment::STATUS_COMPLETED
                            )
                    ),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make()
                    ->label('View Progress'),

                Action::make('recalculate')
                    ->label('Recalculate')
                    ->icon(
                        Heroicon::OutlinedArrowPath
                    )
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Recalculate student progress?'
                    )
                    ->modalDescription(
                        'Progress will be recalculated using the currently published lessons.'
                    )
                    ->action(
                        function (
                            CourseEnrollment $record
                        ): void {
                            $record->recalculateProgress();

                            Notification::make()
                                ->title(
                                    'Progress recalculated'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('pause')
                    ->label('Pause')
                    ->icon(
                        Heroicon::OutlinedPauseCircle
                    )
                    ->color('warning')
                    ->visible(
                        fn (
                            CourseEnrollment $record
                        ): bool =>
                            $record->status
                            === CourseEnrollment::STATUS_ACTIVE
                    )
                    ->modalHeading(
                        'Pause course enrolment'
                    )
                    ->modalDescription(
                        'The student will immediately lose access to this course, its lessons, videos and quizzes.'
                    )
                    ->schema([
                        Textarea::make('pause_reason')
                            ->label('Reason for pausing')
                            ->placeholder(
                                'For example: Student requested a temporary break.'
                            )
                            ->helperText(
                                'This reason will be shown to the student.'
                            )
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->action(
                        function (
                            CourseEnrollment $record,
                            array $data
                        ): void {
                            $record->update([
                                'status' =>
                                    CourseEnrollment::STATUS_PAUSED,

                                'pause_reason' =>
                                    $data['pause_reason'],

                                'paused_at' => now(),

                                'paused_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title('Enrolment paused')
                                ->body(
                                    'The student can no longer access this course.'
                                )
                                ->warning()
                                ->send();
                        }
                    ),

                Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon(
                        Heroicon::OutlinedPlayCircle
                    )
                    ->color('success')
                    ->visible(
                        fn (
                            CourseEnrollment $record
                        ): bool =>
                            in_array(
                                $record->status,
                                [
                                    CourseEnrollment::STATUS_PAUSED,
                                    CourseEnrollment::STATUS_CANCELLED,
                                ],
                                true
                            )
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Reactivate course enrolment?'
                    )
                    ->modalDescription(
                        'The student will regain access to their course, lessons, videos and quizzes.'
                    )
                    ->action(
                        function (
                            CourseEnrollment $record
                        ): void {
                            $record->update([
                                'status' =>
                                    CourseEnrollment::STATUS_ACTIVE,

                                'pause_reason' => null,
                                'paused_at' => null,
                                'paused_by' => null,
                                'completed_at' => null,
                                'last_activity_at' => now(),
                            ]);

                            $record->recalculateProgress();

                            Notification::make()
                                ->title(
                                    'Enrolment reactivated'
                                )
                                ->body(
                                    'The student can now continue learning.'
                                )
                                ->success()
                                ->send();
                        }
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->emptyStateHeading(
                'No students enrolled'
            )
            ->emptyStateDescription(
                'Student progress will appear here after members enrol in courses.'
            )
            ->emptyStateIcon(
                Heroicon::OutlinedChartBarSquare
            );
    }
}