<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Skill;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workshop>
 */
class WorkshopFactory extends Factory
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
            'title' => [
                'en' => fake()->sentence(),
                'es' => fake()->sentence(),
            ],
            'description' => [
                'en' => fake()->paragraph(1),
                'es' => fake()->paragraph(1),
            ],
            'type' => fake()->randomElement(['permanent', 'temporary']),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'price' => fake()->randomFloat(2, 10, 100),
            'max_students' => fake()->numberBetween(5, 30),
            'cover_image_path' => 'workshops/images/'.$this->faker->uuid().'.jpg',
        ];
    }

    public function withSkills(?array $skillIds = null): static
    {
        return $this->afterCreating(function (Workshop $artist) use ($skillIds) {
            $artist->skills()->attach(
                $skillIds ?? Skill::inRandomOrder()->limit(3)->pluck('id')
            );
        });
    }
}