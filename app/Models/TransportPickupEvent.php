<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class TransportPickupEvent extends Model
{
    protected $fillable = [
        'title',
        'pickup_date',
        'capacity_per_slot',
        'pickup_start_time',
        'pickup_end_time',
        'interval_minutes',
        'bookings_open',
        'notes',
        'created_by',
        'legacy_source',
        'legacy_id',
        'legacy_payload',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'capacity_per_slot' => 'integer',
            'interval_minutes' => 'integer',
            'bookings_open' => 'boolean',
            'legacy_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TransportPickupEvent $event): void {
            if (! $event->created_by && auth()->check()) {
                $event->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TransportBooking::class);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('pickup_date', '>=', today());
    }

    public function scopeOpenForBooking(Builder $query): Builder
    {
        return $query
            ->upcoming()
            ->where('bookings_open', true);
    }

    public function getBookedSeatsAttribute(): int
    {
        return (int) $this->bookings()
            ->where('status', 'confirmed')
            ->sum('party_size');
    }

    public function getBookingCountAttribute(): int
    {
        return (int) $this->bookings()->count();
    }

    public function availableSlots(): Collection
    {
        $start = CarbonImmutable::parse(
            $this->pickup_date->format('Y-m-d') . ' ' . $this->pickup_start_time
        );

        $end = CarbonImmutable::parse(
            $this->pickup_date->format('Y-m-d') . ' ' . $this->pickup_end_time
        );

        $interval = max(1, (int) $this->interval_minutes);
        $capacity = max(1, (int) $this->capacity_per_slot);

        $bookedByTime = $this->bookings()
            ->where('status', 'confirmed')
            ->selectRaw("TIME_FORMAT(pickup_time, '%H:%i:%s') as slot_time, SUM(party_size) as seats")
            ->groupBy('slot_time')
            ->pluck('seats', 'slot_time');

        $slots = collect();
        $cursor = $start;

        while ($cursor->lte($end)) {
            $key = $cursor->format('H:i:s');
            $booked = (int) ($bookedByTime[$key] ?? 0);
            $remaining = max(0, $capacity - $booked);

            $slots->push([
                'value' => $key,
                'label' => $cursor->format('g:i A'),
                'booked' => $booked,
                'remaining' => $remaining,
                'full' => $remaining <= 0,
            ]);

            $cursor = $cursor->addMinutes($interval);
        }

        return $slots;
    }
}
