<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'profile_image' => $this->faker->optional()->imageUrl(400, 400, 'people'),
            'minibio' => [
                'en' => $this->faker->paragraph(1),
                'es' => $this->faker->paragraph(1)
            ],
            'bio' => [
                'en' => $this->faker->paragraphs(3, true),
                'es' => $this->faker->paragraphs(3, true)
            ],
            'social_links' => [
                'website' => $this->faker->optional()->url(),
                'instagram' => $this->faker->optional()->userName(),
                'twitter' => $this->faker->optional()->userName(),
                'flickr' => $this->faker->optional()->url()
            ]
        ];
    }

    public function withSkills(?array $skillIds = null): static
    {
        return $this->afterCreating(function (Artist $artist) use ($skillIds) {
            $artist->skills()->attach(
                $skillIds ?? Skill::inRandomOrder()->limit(3)->pluck('id')
            );
        });
    }

    public function withArtworks(int $count = 3): static
    {
        return $this->has(
            Artwork::factory()
                ->count($count)
                ->withImages(),
            'artworks'
        );
    }
}
