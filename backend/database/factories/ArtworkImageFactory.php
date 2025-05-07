<?php

namespace Database\Factories;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArtworkImage>
 */
class ArtworkImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'artwork_id' => Artwork::factory(),
            'path' => function (array $attributes) {
                $artwork = Artwork::find($attributes['artwork_id']);
                $slug = Str::slug($artwork->title);
                return "artworks/images/{$slug}-{$this->faker->unique()->numberBetween(1, 10)}.jpg";
            },
            'is_primary' => false,
            'order' => 0,
        ];
    }

    public function primary()
    {
        return $this->state(fn () => ['is_primary' => true]);
    }
}
