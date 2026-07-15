<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'explanation',
        'points',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(
            QuizAttemptAnswer::class
        );
    }

    public function correctAnswer(): ?QuizAnswer
    {
        return $this
            ->answers()
            ->where('is_correct', true)
            ->first();
    }
}