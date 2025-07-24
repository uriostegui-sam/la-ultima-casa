<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'profile_image', 'published'];
    protected $casts = ['name' => 'array'];

    public function artists() {
        return $this->belongsToMany(Artist::class);
    }

    public function workshops()
    {
        return $this->belongsToMany(Workshop::class, 'workshop_skill');
    }
}
