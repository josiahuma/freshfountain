<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    //
     protected $fillable = [
        'title',
        'slug',
        'template',
        'content',
        'sections',
        'seo_title',
        'seo_description',
        'is_published',
        'banner_image',
        'excerpt',
    ];

    protected $casts = [
        'sections' => 'array',
        'is_published' => 'boolean',
    ];
}
