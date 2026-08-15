<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportBooking extends Model
{
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COLLECTED = 'collected';

    protected $fillable = [
        'transport_pickup_event_id',
        'name',
        'phone',
        'address',
        'pickup_time',
        'party_size',
        'status',
        'notes',
        'legacy_source',
        'legacy_id',
        'legacy_payload',
    ];

    protected function casts(): array
    {
        return [
            'party_size' => 'integer',
            'legacy_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (TransportBooking $booking): void {
            $booking->name = trim((string) $booking->name);
            $booking->phone = trim((string) $booking->phone);
            $booking->address = trim((string) $booking->address);
        });
    }

    public function pickupEvent(): BelongsTo
    {
        return $this->belongsTo(TransportPickupEvent::class, 'transport_pickup_event_id');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(TransportNotificationLog::class, 'transport_booking_id');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COLLECTED => 'Collected',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($this->address);
    }

    public function getWazeUrlAttribute(): string
    {
        return 'https://waze.com/ul?q=' . urlencode($this->address) . '&navigate=yes';
    }
}
