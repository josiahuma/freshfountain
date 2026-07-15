<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_question_id',
        'answer',
        'is_correct',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(
            QuizQuestion::class,
            'quiz_question_id'
        );
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(
            QuizAttemptAnswer::class
        );
    }
}