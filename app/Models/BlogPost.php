<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'is_published',
        'published_at',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Auto-generate slug if missing.
     */
    protected static function booted(): void
    {
        static::saving(function (BlogPost $post) {
            if (! $post->slug && $post->title) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Published posts only, with sensible rules:
     * - must be marked published
     * - published_at must be <= now (or null treated as "now")
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', Carbon::now());
            });
    }

    /**
     * Convenience accessor for the image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) return null;

        // stored on public disk
        return asset('storage/' . ltrim($this->featured_image, '/'));
    }
}
