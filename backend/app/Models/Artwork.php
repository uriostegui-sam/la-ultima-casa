<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'title',
        'description',
        'image_path',
        'dimensions',
        'creation_date',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function images()
    {
        return $this->hasMany(ArtworkImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ArtworkImage::class)->where('is_primary', true);
    }
}