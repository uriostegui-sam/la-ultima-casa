<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $fillable = ['cover_image', 'number', 'mail', 'address', 'description', 'logo_header', 'logo_footer', 'logo_hero', 'logo_favicon'];

    protected $casts = [
        'cover_image' => 'string',
        'address' => 'array',
        'description' => 'array',
        'logo_header' => 'string',
        'logo_footer' => 'string',
        'logo_hero' => 'string',
        'logo_favicon' => 'string',
    ];
}
