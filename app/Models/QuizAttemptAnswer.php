<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
        'quiz_answer_id',
        'selected_answer_ids',
        'is_correct',
        'points_awarded',
    ];

    protected function casts(): array
    {
        return [
            'selected_answer_ids' => 'array',
            'is_correct' => 'boolean',
            'points_awarded' => 'integer',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(
            QuizAttempt::class,
            'quiz_attempt_id'
        );
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(
            QuizQuestion::class,
            'quiz_question_id'
        );
    }

    /**
     * Legacy single-answer relationship.
     *
     * New attempts use selected_answer_ids instead.
     */
    public function selectedAnswer(): BelongsTo
    {
        return $this->belongsTo(
            QuizAnswer::class,
            'quiz_answer_id'
        );
    }

    public function selectedIds(): array
    {
        return collect(
            $this->selected_answer_ids ?? []
        )
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}