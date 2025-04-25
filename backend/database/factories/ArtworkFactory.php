<?php

namespace Database\Factories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'artist_id' => Artist::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'dimensions' => fake()->numberBetween(10, 200) . 'x' .
                        fake()->numberBetween(10, 200) . 'x' .
                        fake()->numberBetween(1, 50) . ' cm',
            'creation_date' => fake()->date(),
        ];
    }
}
