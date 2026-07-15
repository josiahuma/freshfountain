<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningEmailLog extends Model
{
    use HasFactory;

    public const TYPE_ENROLMENT_WELCOME =
        'enrolment_welcome';

    public const TYPE_LESSON_COMPLETED =
        'lesson_completed';

    public const TYPE_QUIZ_PASSED =
        'quiz_passed';

    public const TYPE_COURSE_COMPLETED =
        'course_completed';

    public const TYPE_CERTIFICATE_READY =
        'certificate_ready';

    protected $fillable = [
        'user_id',
        'course_enrollment_id',
        'lesson_id',
        'quiz_attempt_id',
        'certificate_id',
        'type',
        'dedupe_key',
        'sent_at',
        'failed_at',
        'error_message',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(
            CourseEnrollment::class,
            'course_enrollment_id'
        );
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(
            QuizAttempt::class
        );
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(
            Certificate::class
        );
    }
}