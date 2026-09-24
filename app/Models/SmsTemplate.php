<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    public const CATEGORY_BIRTHDAY = 'birthday';
    public const CATEGORY_SERVICE_REMINDER = 'service_reminder';
    public const CATEGORY_ANNOUNCEMENT = 'announcement';
    public const CATEGORY_GENERAL = 'general';

    protected $fillable = [
        'name',
        'category',
        'body',
        'is_active',
        'is_birthday',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_birthday' => 'boolean',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_BIRTHDAY => 'Birthday',
            self::CATEGORY_SERVICE_REMINDER => 'Service reminder',
            self::CATEGORY_ANNOUNCEMENT => 'Announcement',
            self::CATEGORY_GENERAL => 'General',
        ];
    }
}
