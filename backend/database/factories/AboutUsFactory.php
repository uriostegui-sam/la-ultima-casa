<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AboutUs>
 */
class AboutUsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => fake()->numberBetween(1, 100),
            'cover_image' => 'about_us/images/'.$this->faker->uuid().'.jpg',
            'mail' => fake()->email(),
            'address' => [
                'text' => fake()->paragraph(1),
                'map' => fake()->paragraph(1),
            ],
        ];
    }
}
