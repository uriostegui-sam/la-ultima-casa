<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $fillable = ['cover_image', 'number', 'mail', 'address'];

    protected $casts = [
        'cover_image' => 'string',
        'address' => 'array',
    ];
}
