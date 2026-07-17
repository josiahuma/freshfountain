<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChurchUnitMember extends Model
{
    protected $table = 'church_unit_member';

    protected $fillable = [
        'member_id',
        'church_unit_id',
        'assigned_leader_id',
        'status',
        'source',
        'joined_at',
        'left_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'left_at' => 'date',
        ];
    }
}