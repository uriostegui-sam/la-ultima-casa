<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'title',
        'description',
        'type',
        'start_date',
        'end_date',
        'price',
        'max_students',
        'cover_image_path',
    ];
    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['cover_image_url'];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'workshop_skill');
    }

    public function getCoverImageFullUrlAttribute(): ?string
    {
        return $this->cover_image_path
            ? Storage::disk('public')->url($this->cover_image_path)
            : null;
    }
}
