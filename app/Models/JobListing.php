<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobListing extends Model
{
    protected $table = 'job_listings';

    protected $fillable = [
        'title',
        'slug',
        'location',
        'employment_type',
        'salary',
        'closing_date',
        'template',
        'description',
        'is_active',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_listing_id');
    }
}
