<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UnitMembershipRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_WITHDRAWN = 'withdrawn';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'member_id',
        'church_unit_id',
        'assigned_leader_id',
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'message',
        'status',
        'submitted_at',
        'assigned_at',
        'contacted_at',
        'reviewed_at',
        'approved_at',
        'declined_at',
        'completed_at',
        'reviewed_by',
        'internal_notes',
        'submission_reference',
        'source',
        'legacy_source',
        'legacy_id',
        'legacy_payload',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'assigned_at' => 'datetime',
            'contacted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'declined_at' => 'datetime',
            'completed_at' => 'datetime',
            'legacy_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(
            function (
                UnitMembershipRequest $request
            ): void {
                $request->submission_reference ??=
                    static::generateReference();

                $request->submitted_at ??=
                    now();

                $request->status ??=
                    self::STATUS_PENDING;
            }
        );

        static::saving(
            function (
                UnitMembershipRequest $request
            ): void {
                $request->first_name = filled(
                    $request->first_name
                )
                    ? trim($request->first_name)
                    : null;

                $request->last_name = filled(
                    $request->last_name
                )
                    ? trim($request->last_name)
                    : null;

                $request->email = filled(
                    $request->email
                )
                    ? Str::lower(
                        trim($request->email)
                    )
                    : null;

                $request->mobile_number = filled(
                    $request->mobile_number
                )
                    ? trim(
                        $request->mobile_number
                    )
                    : null;
            }
        );
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(
            Member::class
        );
    }

    public function churchUnit(): BelongsTo
    {
        return $this->belongsTo(
            ChurchUnit::class
        );
    }

    public function assignedLeader(): BelongsTo
    {
        return $this->belongsTo(
            Leader::class,
            'assigned_leader_id'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function scopePending(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_PENDING,
                self::STATUS_ASSIGNED,
                self::STATUS_CONTACTED,
            ]
        );
    }

    public function scopeActionable(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_PENDING,
                self::STATUS_ASSIGNED,
                self::STATUS_CONTACTED,
                self::STATUS_APPROVED,
            ]
        );
    }

    public function scopeClosed(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_DECLINED,
                self::STATUS_WITHDRAWN,
                self::STATUS_COMPLETED,
            ]
        );
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

    public function canAssignLeader(): bool
    {
        return $this->status === self::STATUS_PENDING
            && blank($this->assigned_leader_id);
    }

    public function canMarkContacted(): bool
    {
        return $this->status === self::STATUS_ASSIGNED
            && filled($this->assigned_leader_id);
    }

    public function canApprove(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_ASSIGNED,
                self::STATUS_CONTACTED,
            ],
            true
        );
    }

    public function canComplete(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canDecline(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PENDING,
                self::STATUS_ASSIGNED,
                self::STATUS_CONTACTED,
            ],
            true
        );
    }

    public function canEdit(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PENDING,
                self::STATUS_ASSIGNED,
                self::STATUS_CONTACTED,
            ],
            true
        );
    }

    public function isTerminal(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_DECLINED,
                self::STATUS_WITHDRAWN,
                self::STATUS_COMPLETED,
            ],
            true
        );
    }

    public function isClosed(): bool
    {
        return $this->isTerminal();
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING =>
                'Pending',

            self::STATUS_ASSIGNED =>
                'Assigned',

            self::STATUS_CONTACTED =>
                'Contacted',

            self::STATUS_APPROVED =>
                'Approved',

            self::STATUS_DECLINED =>
                'Declined',

            self::STATUS_WITHDRAWN =>
                'Withdrawn',

            self::STATUS_COMPLETED =>
                'Completed',
        ];
    }

    public static function statusColor(
        ?string $status
    ): string {
        return match ($status) {
            self::STATUS_PENDING =>
                'warning',

            self::STATUS_ASSIGNED =>
                'info',

            self::STATUS_CONTACTED =>
                'primary',

            self::STATUS_APPROVED,
            self::STATUS_COMPLETED =>
                'success',

            self::STATUS_DECLINED =>
                'danger',

            self::STATUS_WITHDRAWN =>
                'gray',

            default =>
                'gray',
        };
    }

    private static function generateReference(): string
    {
        do {
            $reference =
                'FF-UNIT-'
                . now()->format('ymd')
                . '-'
                . Str::upper(
                    Str::random(6)
                );
        } while (
            static::query()
                ->where(
                    'submission_reference',
                    $reference
                )
                ->exists()
        );

        return $reference;
    }
}