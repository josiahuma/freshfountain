<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    public const TYPE_GENERAL = 'general';

    public const TYPE_MEMBERSHIP = 'membership';

    public const TYPE_BAPTISM = 'baptism';

    public const TYPE_NEW_BELIEVERS = 'new_believers';

    public const TYPE_WORKERS = 'workers';

    public const TYPE_LEADERSHIP = 'leadership';

    public const LEVEL_BEGINNER = 'beginner';

    public const LEVEL_INTERMEDIATE = 'intermediate';

    public const LEVEL_ADVANCED = 'advanced';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'cover_image',
        'course_type',
        'difficulty_level',
        'estimated_duration_minutes',
        'is_published',
        'is_featured',
        'allow_self_enrolment',
        'requires_sequential_progress',
        'certificate_enabled',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_duration_minutes' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'allow_self_enrolment' => 'boolean',
            'requires_sequential_progress' => 'boolean',
            'certificate_enabled' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Course $course): void {
            if (blank($course->slug)) {
                $course->slug = static::createUniqueSlug(
                    $course->title
                );
            }

            if (
                $course->is_published
                && blank($course->published_at)
            ) {
                $course->published_at = now();
            }
        });

        static::updating(function (Course $course): void {
            if (
                $course->isDirty('title')
                && blank($course->slug)
            ) {
                $course->slug = static::createUniqueSlug(
                    $course->title,
                    $course->id
                );
            }

            if (
                $course->is_published
                && blank($course->published_at)
            ) {
                $course->published_at = now();
            }

            if (! $course->is_published) {
                $course->published_at = null;
            }
        });
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function publishedLessons(): HasMany
    {
        return $this->hasMany(Lesson::class)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function lessonCompletions(): HasMany
    {
        return $this->hasManyThrough(
            LessonCompletion::class,
            Lesson::class
        );
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('published_at')
                    ->orWhere(
                        'published_at',
                        '<=',
                        now()
                    );
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (blank($this->cover_image)) {
            return null;
        }

        if (
            str_starts_with($this->cover_image, 'http://')
            || str_starts_with($this->cover_image, 'https://')
        ) {
            return $this->cover_image;
        }

        return Storage::disk('public')->url(
            $this->cover_image
        );
    }

    public function getEstimatedDurationLabelAttribute(): ?string
    {
        if (! $this->estimated_duration_minutes) {
            return null;
        }

        $hours = intdiv(
            $this->estimated_duration_minutes,
            60
        );

        $minutes =
            $this->estimated_duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} hr {$minutes} min";
        }

        if ($hours > 0) {
            return Str::plural(
                'hour',
                $hours,
                true
            );
        }

        return "{$minutes} min";
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_GENERAL => 'General Course',
            self::TYPE_MEMBERSHIP => 'Membership Class',
            self::TYPE_BAPTISM => 'Baptismal Class',
            self::TYPE_NEW_BELIEVERS => 'New Believers',
            self::TYPE_WORKERS => 'Workers’ Training',
            self::TYPE_LEADERSHIP => 'Leadership',
        ];
    }

    public static function difficultyOptions(): array
    {
        return [
            self::LEVEL_BEGINNER => 'Beginner',
            self::LEVEL_INTERMEDIATE => 'Intermediate',
            self::LEVEL_ADVANCED => 'Advanced',
        ];
    }

    private static function createUniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($title)
            ?: 'course';

        $slug = $baseSlug;
        $number = 2;

        while (
            static::query()
                ->when(
                    $ignoreId,
                    fn (Builder $query): Builder =>
                        $query->whereKeyNot($ignoreId)
                )
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$number}";
            $number++;
        }

        return $slug;
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}