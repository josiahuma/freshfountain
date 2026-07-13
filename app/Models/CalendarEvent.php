<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CalendarEvent extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Event source constants
    |--------------------------------------------------------------------------
    */

    public const SOURCE_INTERNAL = 'internal';

    public const SOURCE_EVENTIB = 'eventib';

    /*
    |--------------------------------------------------------------------------
    | Recurrence constants
    |--------------------------------------------------------------------------
    */

    public const RECURRENCE_NONE = 'none';

    public const RECURRENCE_DAILY = 'daily';

    public const RECURRENCE_WEEKLY = 'weekly';

    public const RECURRENCE_MONTHLY = 'monthly';

    public const RECURRENCE_YEARLY = 'yearly';

    /*
    |--------------------------------------------------------------------------
    | Mass assignable fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'is_all_day',
        'category',
        'colour',
        'image',
        'external_url',
        'source',
        'eventib_event_id',
        'is_featured',
        'is_published',
        'recurrence_type',
        'recurrence_until',
        'sort_order',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_all_day' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'recurrence_until' => 'date',
            'sort_order' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeInternal(Builder $query): Builder
    {
        return $query->where('source', self::SOURCE_INTERNAL);
    }

    public function scopeEventib(Builder $query): Builder
    {
        return $query->where('source', self::SOURCE_EVENTIB);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query
                ->where('starts_at', '>=', now()->startOfDay())
                ->orWhere('ends_at', '>=', now());
        });
    }

    public function scopeBetween(
        Builder $query,
        Carbon|string $start,
        Carbon|string $end
    ): Builder {
        return $query->where(function (Builder $query) use ($start, $end): void {
            $query
                ->whereBetween('starts_at', [$start, $end])
                ->orWhereBetween('ends_at', [$start, $end])
                ->orWhere(function (Builder $query) use ($start, $end): void {
                    $query
                        ->where('starts_at', '<=', $start)
                        ->where('ends_at', '>=', $end);
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        if (
            str_starts_with($this->image, 'http://') ||
            str_starts_with($this->image, 'https://')
        ) {
            return $this->image;
        }

        return Storage::url($this->image);
    }

    public function getDateLabelAttribute(): string
    {
        if ($this->is_all_day) {
            return $this->starts_at->format('j F Y');
        }

        return $this->starts_at->format('j F Y \a\t g:i A');
    }

    public function getTimeLabelAttribute(): string
    {
        if ($this->is_all_day) {
            return 'All day';
        }

        if ($this->ends_at) {
            return $this->starts_at->format('g:i A')
                . ' – '
                . $this->ends_at->format('g:i A');
        }

        return $this->starts_at->format('g:i A');
    }

    public function getIsExternalAttribute(): bool
    {
        return $this->source === self::SOURCE_EVENTIB;
    }

    /*
    |--------------------------------------------------------------------------
    | Static option helpers
    |--------------------------------------------------------------------------
    */

    public static function categoryOptions(): array
    {
        return [
            'general' => 'General',
            'service' => 'Church Service',
            'prayer' => 'Prayer',
            'worship' => 'Worship',
            'conference' => 'Conference',
            'outreach' => 'Outreach',
            'youth' => 'Youth',
            'children' => 'Children',
            'workers' => 'Workers',
            'community' => 'Community',
            'anniversary' => 'Anniversary',
        ];
    }

    public static function categoryColours(): array
    {
        return [
            'general' => '#1d4ed8',
            'service' => '#2563eb',
            'prayer' => '#e11d48',
            'worship' => '#9333ea',
            'conference' => '#7c3aed',
            'outreach' => '#16a34a',
            'youth' => '#ea580c',
            'children' => '#0891b2',
            'workers' => '#475569',
            'community' => '#0f766e',
            'anniversary' => '#ca8a04',
        ];
    }

    public static function recurrenceOptions(): array
    {
        return [
            self::RECURRENCE_NONE => 'Does not repeat',
            self::RECURRENCE_DAILY => 'Every day',
            self::RECURRENCE_WEEKLY => 'Every week',
            self::RECURRENCE_MONTHLY => 'Every month on this weekday position',
            self::RECURRENCE_YEARLY => 'Every year',
        ];
    }

    public static function sourceOptions(): array
    {
        return [
            self::SOURCE_INTERNAL => 'Website calendar',
            self::SOURCE_EVENTIB => 'Eventib',
        ];
    }
}