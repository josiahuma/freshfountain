<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_VISITOR = 'visitor';

    public const STATUS_PENDING = 'pending';

    public const STATUS_LEFT = 'left';

    protected $fillable = [
        'user_id',
        'church_unit_id',
        'leader_id',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'anniversary_date',
        'email',
        'mobile_number',
        'alternative_phone',
        'address',
        'postcode',
        'membership_status',
        'joined_at',
        'legacy_church_leader_name',
        'notes',
        'is_active',
        'email_consent',
        'sms_consent',
        'do_not_contact',
        'legacy_source',
        'legacy_id',
        'legacy_payload',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'anniversary_date' => 'date',
            'joined_at' => 'date',
            'is_active' => 'boolean',
            'email_consent' => 'boolean',
            'sms_consent' => 'boolean',
            'do_not_contact' => 'boolean',
            'legacy_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(
            function (Member $member): void {
                $member->email = filled($member->email)
                    ? strtolower(trim($member->email))
                    : null;

                $member->first_name = trim(
                    (string) $member->first_name
                );

                $member->middle_name = filled(
                    $member->middle_name
                )
                    ? trim($member->middle_name)
                    : null;

                $member->last_name = filled(
                    $member->last_name
                )
                    ? trim($member->last_name)
                    : null;

                $member->mobile_number = filled(
                    $member->mobile_number
                )
                    ? trim($member->mobile_number)
                    : null;
            }
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function churchUnit(): BelongsTo
    {
        return $this->belongsTo(
            ChurchUnit::class
        );
    }

    public function churchUnits(): BelongsToMany
    {
        return $this->belongsToMany(
            ChurchUnit::class,
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

    public function unitMembershipRequests(): HasMany
    {
        return $this->hasMany(
            UnitMembershipRequest::class
        );
    }

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query
            ->where('is_active', true)
            ->where(
                'membership_status',
                self::STATUS_ACTIVE
            );
    }

    public function scopeBirthdayThisMonth(
        Builder $query
    ): Builder {
        return $query
            ->whereNotNull('date_of_birth')
            ->whereMonth(
                'date_of_birth',
                now()->month
            );
    }

    public function scopeAnniversaryThisMonth(
        Builder $query
    ): Builder {
        return $query
            ->whereNotNull(
                'anniversary_date'
            )
            ->whereMonth(
                'anniversary_date',
                now()->month
            );
    }

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->title,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])
            ->filter()
            ->implode(' ');
    }

    public function getDisplayNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->last_name,
        ])
            ->filter()
            ->implode(' ');
    }

    public function getContactNumberAttribute(): ?string
    {
        return $this->mobile_number
            ?: $this->alternative_phone;
    }

    public function getCanReceiveEmailAttribute(): bool
    {
        return filled($this->email)
            && $this->email_consent
            && ! $this->do_not_contact;
    }

    public function getCanReceiveSmsAttribute(): bool
    {
        return filled($this->mobile_number)
            && $this->sms_consent
            && ! $this->do_not_contact;
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE =>
                'Active member',

            self::STATUS_INACTIVE =>
                'Inactive',

            self::STATUS_VISITOR =>
                'Visitor',

            self::STATUS_PENDING =>
                'Pending',

            self::STATUS_LEFT =>
                'No longer attending',
        ];
    }

    public static function genderOptions(): array
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'prefer_not_to_say' =>
                'Prefer not to say',
        ];
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(
            Leader::class
        );
    }
}