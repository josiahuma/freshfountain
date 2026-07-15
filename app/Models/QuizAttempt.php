<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'course_enrollment_id',
        'attempt_number',
        'score_points',
        'maximum_points',
        'percentage',
        'passed',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'score_points' => 'integer',
            'maximum_points' => 'integer',
            'percentage' => 'integer',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
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

    public function answers(): HasMany
    {
        return $this->hasMany(
            QuizAttemptAnswer::class
        );
    }
}