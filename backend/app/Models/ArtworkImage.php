<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArtworkImage extends Model
{
    use HasFactory;

    protected $fillable = ['artwork_id', 'path', 'is_primary', 'order'];
    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }
    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }
}
