<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkImage extends Model
{
    use HasFactory;

    protected $fillable = ['artwork_id', 'path', 'is_primary', 'order'];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }
}
