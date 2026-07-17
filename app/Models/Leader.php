<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Leader extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'church_unit_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile_number',
        'leadership_role',
        'started_at',
        'ended_at',
        'is_active',
        'notes',
        'legacy_source',
        'legacy_id',
        'legacy_payload',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'is_active' => 'boolean',
            'legacy_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Leader $leader): void {
            $leader->first_name = trim(
                (string) $leader->first_name
            );

            $leader->middle_name = filled(
                $leader->middle_name
            )
                ? trim($leader->middle_name)
                : null;

            $leader->last_name = filled(
                $leader->last_name
            )
                ? trim($leader->last_name)
                : null;

            $leader->email = filled($leader->email)
                ? strtolower(trim($leader->email))
                : null;

            $leader->mobile_number = filled(
                $leader->mobile_number
            )
                ? trim($leader->mobile_number)
                : null;
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function churchUnit(): BelongsTo
    {
        return $this->belongsTo(
            ChurchUnit::class
        );
    }

    public function assignedMembers(): HasMany
    {
        return $this->hasMany(
            Member::class,
            'leader_id'
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

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])
            ->filter()
            ->implode(' ');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name;
    }

    public function unitMembershipRequests(): HasMany
    {
        return $this->hasMany(
            UnitMembershipRequest::class,
            'assigned_leader_id'
        );
    }

    /*public function assignedUnitMemberships(): HasMany
    {
        return $this->hasMany(
            ChurchUnitMember::class,
            'assigned_leader_id'
        );
    }*/
}