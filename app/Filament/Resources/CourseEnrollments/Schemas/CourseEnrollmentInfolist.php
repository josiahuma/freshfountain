<?php

namespace App\Filament\Resources\CourseEnrollments\Schemas;

use App\Models\CourseEnrollment;
use App\Models\Lesson;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseEnrollmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Student name')
                            ->weight('bold'),

                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),

                        TextEntry::make('course.title')
                            ->label('Course')
                            ->weight('bold'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(
                                fn (?string $state): string =>
                                    ucfirst(
                                        $state ?? 'Unknown'
                                    )
                            )
                            ->badge()
                            ->color(
                                fn (
                                    ?string $state
                                ): string =>
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
                            ),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Progress Summary')
                    ->icon(
                        'heroicon-o-chart-bar-square'
                    )
                    ->schema([
                        TextEntry::make(
                            'progress_percentage'
                        )
                            ->label('Progress')
                            ->suffix('%')
                            ->size('lg')
                            ->weight('bold')
                            ->color(
                                fn (
                                    CourseEnrollment $record
                                ): string =>
                                    match (true) {
                                        $record
                                            ->progress_percentage
                                            >= 100 =>
                                            'success',

                                        $record
                                            ->progress_percentage
                                            >= 50 =>
                                            'info',

                                        default =>
                                            'warning',
                                    }
                            ),

                        TextEntry::make(
                            'completed_lessons_summary'
                        )
                            ->label(
                                'Lessons completed'
                            )
                            ->state(
                                fn (
                                    CourseEnrollment $record
                                ): string =>
                                    $record
                                        ->completedLessonsCount()
                                    . ' of '
                                    . $record
                                        ->totalPublishedLessonsCount()
                            ),

                        TextEntry::make(
                            'quiz_attempts_summary'
                        )
                            ->label('Quiz attempts')
                            ->state(
                                fn (
                                    CourseEnrollment $record
                                ): string =>
                                    (string) $record
                                        ->quizAttempts()
                                        ->count()
                            ),

                        TextEntry::make(
                            'average_quiz_score'
                        )
                            ->label(
                                'Average quiz score'
                            )
                            ->state(
                                function (
                                    CourseEnrollment $record
                                ): string {
                                    $average = $record
                                        ->quizAttempts()
                                        ->avg(
                                            'percentage'
                                        );

                                    return $average === null
                                        ? 'No attempts'
                                        : round(
                                            (float) $average
                                        ) . '%';
                                }
                            ),

                        TextEntry::make(
                            'lastLesson.title'
                        )
                            ->label('Last lesson')
                            ->placeholder(
                                'Not started'
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Important Dates')
                    ->icon(
                        'heroicon-o-calendar-days'
                    )
                    ->schema([
                        TextEntry::make('enrolled_at')
                            ->label('Enrolled')
                            ->dateTime(
                                'd M Y, H:i'
                            )
                            ->placeholder('Unknown'),

                        TextEntry::make('started_at')
                            ->label('Started')
                            ->dateTime(
                                'd M Y, H:i'
                            )
                            ->placeholder(
                                'Not started'
                            ),

                        TextEntry::make(
                            'last_activity_at'
                        )
                            ->label(
                                'Last activity'
                            )
                            ->dateTime(
                                'd M Y, H:i'
                            )
                            ->placeholder(
                                'No activity'
                            ),

                        TextEntry::make('completed_at')
                            ->label('Completed')
                            ->dateTime(
                                'd M Y, H:i'
                            )
                            ->placeholder(
                                'Not completed'
                            ),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Lesson Progress')
                    ->description(
                        'Published lessons and the student’s completion status.'
                    )
                    ->icon(
                        'heroicon-o-book-open'
                    )
                    ->schema([
                        RepeatableEntry::make(
                            'lesson_progress'
                        )
                            ->label('')
                            ->state(
                                function (
                                    CourseEnrollment $record
                                ): array {
                                    $completed = $record
                                        ->lessonCompletions()
                                        ->get()
                                        ->keyBy(
                                            'lesson_id'
                                        );

                                    return $record
                                        ->course
                                        ->publishedLessons()
                                        ->get()
                                        ->values()
                                        ->map(
                                            function (
                                                Lesson $lesson,
                                                int $index
                                            ) use (
                                                $completed
                                            ): array {
                                                $completion =
                                                    $completed
                                                        ->get(
                                                            $lesson->id
                                                        );

                                                return [
                                                    'number' =>
                                                        $index + 1,

                                                    'title' =>
                                                        $lesson
                                                            ->title,

                                                    'completed' =>
                                                        $completion
                                                        !== null,

                                                    'completed_at' =>
                                                        $completion
                                                            ?->completed_at,
                                                ];
                                            }
                                        )
                                        ->all();
                                }
                            )
                            ->schema([
                                TextEntry::make('number')
                                    ->label('#')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('title')
                                    ->label('Lesson')
                                    ->weight('bold')
                                    ->wrap(),

                                IconEntry::make(
                                    'completed'
                                )
                                    ->label('Completed')
                                    ->boolean()
                                    ->trueColor(
                                        'success'
                                    )
                                    ->falseColor(
                                        'gray'
                                    ),

                                TextEntry::make(
                                    'completed_at'
                                )
                                    ->label(
                                        'Completed at'
                                    )
                                    ->dateTime(
                                        'd M Y, H:i'
                                    )
                                    ->placeholder('—'),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 4,
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Quiz Attempts')
                    ->description(
                        'Scores recorded for this course enrolment.'
                    )
                    ->icon(
                        'heroicon-o-question-mark-circle'
                    )
                    ->schema([
                        RepeatableEntry::make(
                            'quiz_attempts'
                        )
                            ->label('')
                            ->state(
                                fn (
                                    CourseEnrollment $record
                                ): array =>
                                    $record
                                        ->quizAttempts()
                                        ->with(
                                            'quiz.lesson'
                                        )
                                        ->latest(
                                            'submitted_at'
                                        )
                                        ->get()
                                        ->map(
                                            fn (
                                                $attempt
                                            ): array => [
                                                'quiz' =>
                                                    $attempt
                                                        ->quiz
                                                        ?->title
                                                    ?? 'Quiz',

                                                'lesson' =>
                                                    $attempt
                                                        ->quiz
                                                        ?->lesson
                                                        ?->title
                                                    ?? 'Unknown lesson',

                                                'attempt_number' =>
                                                    $attempt
                                                        ->attempt_number,

                                                'percentage' =>
                                                    $attempt
                                                        ->percentage,

                                                'passed' =>
                                                    $attempt
                                                        ->passed,

                                                'submitted_at' =>
                                                    $attempt
                                                        ->submitted_at,
                                            ]
                                        )
                                        ->all()
                            )
                            ->schema([
                                TextEntry::make('quiz')
                                    ->label('Quiz')
                                    ->weight('bold')
                                    ->wrap(),

                                TextEntry::make('lesson')
                                    ->label('Lesson')
                                    ->wrap(),

                                TextEntry::make(
                                    'attempt_number'
                                )
                                    ->label('Attempt')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make(
                                    'percentage'
                                )
                                    ->label('Score')
                                    ->suffix('%')
                                    ->badge()
                                    ->color(
                                        fn (
                                            $state
                                        ): string =>
                                            (int) $state
                                            >= 80
                                                ? 'success'
                                                : 'danger'
                                    ),

                                IconEntry::make('passed')
                                    ->label('Passed')
                                    ->boolean()
                                    ->trueColor(
                                        'success'
                                    )
                                    ->falseColor(
                                        'danger'
                                    ),

                                TextEntry::make(
                                    'submitted_at'
                                )
                                    ->label('Submitted')
                                    ->dateTime(
                                        'd M Y, H:i'
                                    )
                                    ->placeholder('—'),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                                'xl' => 6,
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}