<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'member_id', 'sms_template_id', 'sent_by', 'recipient', 'message',
        'status', 'provider_transaction_id', 'provider_request_id', 'error_message', 'sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function template(): BelongsTo { return $this->belongsTo(SmsTemplate::class, 'sms_template_id'); }
    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sent_by'); }
}
