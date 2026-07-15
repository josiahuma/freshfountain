<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'pass_percentage',
        'maximum_attempts',
        'is_published',
        'is_required',
        'shuffle_questions',
        'shuffle_answers',
        'show_correct_answers',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'pass_percentage' => 'integer',
            'maximum_attempts' => 'integer',
            'is_published' => 'boolean',
            'is_required' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_correct_answers' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function publishedQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopePublished(
        Builder $query
    ): Builder {
        return $query->where(
            'is_published',
            true
        );
    }

    public function canUserAttempt(
        User $user
    ): bool {
        if ($this->maximum_attempts === null) {
            return true;
        }

        return $this
            ->attempts()
            ->where('user_id', $user->id)
            ->count() < $this->maximum_attempts;
    }

    public function userHasPassed(
        User $user
    ): bool {
        return $this
            ->attempts()
            ->where('user_id', $user->id)
            ->where('passed', true)
            ->exists();
    }
}