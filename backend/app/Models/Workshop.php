<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'featured_position',
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

    public function scopeActiveTemporary(Builder $query): Builder
    {
        return $query->where('type', 'temporary')
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::today());
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->whereNotNull('featured_position')
            ->orderBy('featured_position');
    }
}
