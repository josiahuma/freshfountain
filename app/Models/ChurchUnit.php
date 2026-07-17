<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class ChurchUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'alias',
        'description',
        'email',
        'phone',
        'meeting_day',
        'meeting_time',
        'meeting_location',
        'sort_order',
        'is_active',
        'legacy_source',
        'legacy_id',
        'legacy_payload',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'legacy_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(
            function (ChurchUnit $unit): void {
                if (blank($unit->slug)) {
                    $unit->slug = static::generateUniqueSlug(
                        $unit->name
                    );
                }
            }
        );

        static::updating(
            function (ChurchUnit $unit): void {
                if (
                    $unit->isDirty('name')
                    && blank($unit->slug)
                ) {
                    $unit->slug = static::generateUniqueSlug(
                        $unit->name,
                        $unit->id
                    );
                }
            }
        );
    }

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'is_active',
            true
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(Leader::class);
    }

    public function primaryMembers(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            Member::class,
            'church_unit_member'
        )
            ->withPivot([
                'assigned_leader_id',
                'status',
                'source',
                'joined_at',
                'left_at',
                'notes',
            ])
            ->withTimestamps();
    }

    public function membershipRequests(): HasMany
    {
        return $this->hasMany(
            UnitMembershipRequest::class
        );
    }

    public function getDisplayNameAttribute(): string
    {
        if (
            filled($this->alias)
            && $this->alias !== $this->name
        ) {
            return "{$this->name} ({$this->alias})";
        }

        return $this->name;
    }

    private static function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

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
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}