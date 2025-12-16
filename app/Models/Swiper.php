<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Swiper extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'image',   
        'video_url', 
        'is_video',  
        'slides_text',
    ];

    protected $casts = [
        'is_video' => 'boolean',
        'slides_text' => 'array',
    ];
}
