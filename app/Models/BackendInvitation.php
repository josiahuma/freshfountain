<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackendInvitation extends Model
{
    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'sent_at',
        'accepted_at',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUsable(): bool
    {
        return $this->accepted_at === null
            && $this->sent_at !== null
            && $this->expires_at?->isFuture();
    }

    public function isPending(): bool
    {
        return $this->isUsable();
    }

    public function isExpired(): bool
    {
        return $this->accepted_at === null
            && $this->expires_at?->isPast();
    }
}
