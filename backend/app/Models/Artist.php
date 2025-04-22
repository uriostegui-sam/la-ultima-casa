<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'profile_image', 'minibio', 'bio', 'social_links'];

    protected $casts = [
        'profile_image' => 'string',
        'minibio' => 'array',
        'bio' => 'array',
        'social_links' => 'array'
    ];

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image 
            ? Storage::disk('public')->url($this->profile_image)
            : null;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function skills() {
        return $this->belongsToMany(Skill::class);
    }
}
