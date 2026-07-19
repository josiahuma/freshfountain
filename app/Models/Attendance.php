<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'service_name',
        'service_date',
        'men',
        'women',
        'children',
        'visitors',
        'online',
        'total',
        'notes',
        'created_by',
        'updated_by',
        'legacy_source',
        'legacy_id',
        'legacy_payload',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'men' => 'integer',
            'women' => 'integer',
            'children' => 'integer',
            'visitors' => 'integer',
            'online' => 'integer',
            'total' => 'integer',
            'legacy_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Attendance $attendance): void {
            if (filled($attendance->service_type_id)) {
                $serviceType = AttendanceServiceType::query()->find($attendance->service_type_id);

                if ($serviceType) {
                    $attendance->service_name = $serviceType->name;
                }
            }

            $attendance->service_name = trim((string) $attendance->service_name);
            $attendance->total = $attendance->calculatedTotal();

            if (auth()->check()) {
                if (! $attendance->exists && blank($attendance->created_by)) {
                    $attendance->created_by = auth()->id();
                }

                $attendance->updated_by = auth()->id();
            }
        });
    }

    public function calculatedTotal(): int
    {
        return max(0, (int) $this->men)
            + max(0, (int) $this->women)
            + max(0, (int) $this->children)
            + max(0, (int) $this->visitors)
            + max(0, (int) $this->online);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(AttendanceServiceType::class, 'service_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->whereYear('service_date', $year);
    }
}
