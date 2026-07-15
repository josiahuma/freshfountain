<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CourseEnrollment;

class Lesson extends Model
{
    use HasFactory;

    public const PROVIDER_YOUTUBE = 'youtube';

    public const PROVIDER_VIMEO = 'vimeo';

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'summary',
        'content',
        'video_provider',
        'video_url',
        'video_duration_minutes',
        'sort_order',
        'is_preview',
        'is_published',
        'requires_manual_completion',
    ];

    protected function casts(): array
    {
        return [
            'video_duration_minutes' => 'integer',
            'sort_order' => 'integer',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'requires_manual_completion' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson): void {
            if (blank($lesson->slug)) {
                $lesson->slug = static::createUniqueSlug(
                    $lesson
                );
            }

            if (
                blank($lesson->video_provider)
                && filled($lesson->video_url)
            ) {
                $lesson->video_provider =
                    static::detectVideoProvider(
                        $lesson->video_url
                    );
            }
        });

        static::updating(function (Lesson $lesson): void {
            if (
                $lesson->isDirty('title')
                && blank($lesson->slug)
            ) {
                $lesson->slug = static::createUniqueSlug(
                    $lesson
                );
            }

            if (
                filled($lesson->video_url)
                && (
                    blank($lesson->video_provider)
                    || $lesson->isDirty('video_url')
                )
            ) {
                $lesson->video_provider =
                    static::detectVideoProvider(
                        $lesson->video_url
                    );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Keep enrolment progress accurate
        |--------------------------------------------------------------------------
        */

        static::created(function (Lesson $lesson): void {
            if ($lesson->is_published) {
                static::recalculateCourseEnrollments(
                    $lesson->course_id
                );
            }
        });

        static::updated(function (Lesson $lesson): void {
            if (
                $lesson->wasChanged('is_published')
                || $lesson->wasChanged('course_id')
            ) {
                $originalCourseId = (int) (
                    $lesson->getOriginal('course_id')
                    ?? $lesson->course_id
                );

                static::recalculateCourseEnrollments(
                    $originalCourseId
                );

                if (
                    (int) $lesson->course_id
                    !== $originalCourseId
                ) {
                    static::recalculateCourseEnrollments(
                        $lesson->course_id
                    );
                }
            }
        });

        static::deleted(function (Lesson $lesson): void {
            static::recalculateCourseEnrollments(
                $lesson->course_id
            );
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(
            LessonCompletion::class
        );
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (blank($this->video_url)) {
            return null;
        }

        if (
            $this->video_provider
            === self::PROVIDER_YOUTUBE
        ) {
            $videoId = $this->extractYouTubeId(
                $this->video_url
            );

            return $videoId
                ? "https://www.youtube-nocookie.com/embed/{$videoId}"
                : null;
        }

        if (
            $this->video_provider
            === self::PROVIDER_VIMEO
        ) {
            $videoId = $this->extractVimeoId(
                $this->video_url
            );

            return $videoId
                ? "https://player.vimeo.com/video/{$videoId}"
                : null;
        }

        return null;
    }

    public static function videoProviderOptions(): array
    {
        return [
            self::PROVIDER_YOUTUBE => 'YouTube',
            self::PROVIDER_VIMEO => 'Vimeo',
        ];
    }

    public static function detectVideoProvider(
        string $url
    ): ?string {
        $host = strtolower(
            parse_url($url, PHP_URL_HOST) ?? ''
        );

        if (
            str_contains($host, 'youtube.com')
            || str_contains($host, 'youtu.be')
        ) {
            return self::PROVIDER_YOUTUBE;
        }

        if (str_contains($host, 'vimeo.com')) {
            return self::PROVIDER_VIMEO;
        }

        return null;
    }

    private function extractYouTubeId(
        string $url
    ): ?string {
        $host = strtolower(
            parse_url($url, PHP_URL_HOST) ?? ''
        );

        if (str_contains($host, 'youtu.be')) {
            return trim(
                parse_url($url, PHP_URL_PATH) ?? '',
                '/'
            ) ?: null;
        }

        $query = [];

        parse_str(
            parse_url($url, PHP_URL_QUERY) ?? '',
            $query
        );

        if (! empty($query['v'])) {
            return (string) $query['v'];
        }

        $path = trim(
            parse_url($url, PHP_URL_PATH) ?? '',
            '/'
        );

        if (
            str_starts_with($path, 'embed/')
            || str_starts_with($path, 'shorts/')
        ) {
            return explode('/', $path)[1] ?? null;
        }

        return null;
    }

    private function extractVimeoId(
        string $url
    ): ?string {
        $path = trim(
            parse_url($url, PHP_URL_PATH) ?? '',
            '/'
        );

        preg_match(
            '/(\d+)/',
            $path,
            $matches
        );

        return $matches[1] ?? null;
    }

    private static function createUniqueSlug(
        Lesson $lesson
    ): string {
        $baseSlug = Str::slug($lesson->title)
            ?: 'lesson';

        $slug = $baseSlug;
        $number = 2;

        while (
            static::query()
                ->where(
                    'course_id',
                    $lesson->course_id
                )
                ->when(
                    $lesson->exists,
                    fn ($query) =>
                        $query->whereKeyNot(
                            $lesson->getKey()
                        )
                )
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$number}";
            $number++;
        }

        return $slug;
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function hasRequiredQuiz(): bool
    {
        return $this->quiz()
            ->where('is_published', true)
            ->where('is_required', true)
            ->exists();
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    private static function recalculateCourseEnrollments(
        int $courseId
    ): void {
        CourseEnrollment::query()
            ->where('course_id', $courseId)
            ->with('course')
            ->chunkById(
                100,
                function ($enrollments): void {
                    foreach ($enrollments as $enrollment) {
                        $enrollment->recalculateProgress();
                    }
                }
            );
    }
}