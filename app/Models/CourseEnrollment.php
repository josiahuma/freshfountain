<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseEnrollment extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'pause_reason',
        'paused_at',
        'paused_by',
        'enrolled_at',
        'started_at',
        'completed_at',
        'last_activity_at',
        'progress_percentage',
        'last_lesson_id',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'progress_percentage' => 'integer',
            'paused_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(
            function (
                CourseEnrollment $enrollment
            ): void {
                $enrollment->enrolled_at ??= now();
                $enrollment->last_activity_at ??= now();
            }
        );
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastLesson(): BelongsTo
    {
        return $this->belongsTo(
            Lesson::class,
            'last_lesson_id'
        );
    }

    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(
            LessonCompletion::class
        );
    }

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_ACTIVE
        );
    }

    public function recalculateProgress(): void
    {
        $lessonCount = $this->course
            ->publishedLessons()
            ->count();

        if ($lessonCount === 0) {
            $this->update([
                'progress_percentage' => 0,
            ]);

            return;
        }

        $completedCount = $this
            ->lessonCompletions()
            ->whereHas(
                'lesson',
                fn (Builder $query): Builder =>
                    $query
                        ->where('course_id', $this->course_id)
                        ->where('is_published', true)
            )
            ->count();

        $percentage = min(
            100,
            (int) round(
                ($completedCount / $lessonCount) * 100
            )
        );

        $data = [
            'progress_percentage' => $percentage,
            'last_activity_at' => now(),
        ];

        if ($percentage >= 100) {
            $data['status'] = self::STATUS_COMPLETED;
            $data['completed_at'] =
                $this->completed_at ?? now();
        } elseif (
            $this->status
            === self::STATUS_COMPLETED
        ) {
            $data['status'] = self::STATUS_ACTIVE;
            $data['completed_at'] = null;
        }

        $this->update($data);
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function completedLessonsCount(): int
    {
        return $this->lessonCompletions()
            ->whereHas(
                'lesson',
                fn (Builder $query): Builder =>
                    $query
                        ->where(
                            'course_id',
                            $this->course_id
                        )
                        ->where(
                            'is_published',
                            true
                        )
            )
            ->count();
    }

    public function totalPublishedLessonsCount(): int
    {
        return $this->course
            ->publishedLessons()
            ->count();
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(
            QuizAttempt::class
        );
    }

    public function getDisplayTitleAttribute(): string
    {
        $student = $this->user?->name
            ?? 'Unknown student';

        $course = $this->course?->title
            ?? 'Unknown course';

        return "{$student} — {$course}";
    }

    public function pausedByUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'paused_by'
        );
    }

    public function blocksLearningAccess(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PAUSED,
                self::STATUS_CANCELLED,
            ],
            true
        );
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(
            Certificate::class,
            'course_enrollment_id'
        );
    }
}