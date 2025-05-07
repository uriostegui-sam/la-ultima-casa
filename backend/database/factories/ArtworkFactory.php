<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artwork>
 */
class ArtworkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);

        return [
            'artist_id' => Artist::factory(),
            'title' => $title,
            'description' => [
                'en' => $this->faker->paragraph(1),
                'es' => $this->faker->paragraph(1)
            ],
            'dimensions' => [
                    'width' => fake()->numberBetween(10, 200),
                    'height' => fake()->numberBetween(10, 200),
                    'depth' => fake()->numberBetween(1, 50),
                ],
            'creation_date' => fake()->date(),
        ];
    }

    public function withImages(int $count = 3): static
    {
        return $this->afterCreating(function (Artwork $artwork) use ($count) {
            $slug = Str::slug($artwork->title);
            
            // Create primary image first **
            ArtworkImage::factory()
                ->primary()
                ->create([
                    'artwork_id' => $artwork->id,
                    'path' => "artworks/images/{$slug}-1.jpg",
                    'order' => 0
                ]);
            
            // Create additional images
            for ($i = 2; $i <= $count; $i++) {
                ArtworkImage::factory()
                    ->create([
                        'artwork_id' => $artwork->id,
                        'path' => "artworks/images/{$slug}-{$i}.jpg",
                        'order' => $i - 1
                    ]);
            }
        });
    }
}
