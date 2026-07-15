<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'course_enrollment_id',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(
            function (
                LessonCompletion $completion
            ): void {
                $completion
                    ->courseEnrollment
                    ->recalculateProgress();
            }
        );

        static::deleted(
            function (
                LessonCompletion $completion
            ): void {
                $completion
                    ->courseEnrollment
                    ?->recalculateProgress();
            }
        );
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courseEnrollment(): BelongsTo
    {
        return $this->belongsTo(
            CourseEnrollment::class
        );
    }
}