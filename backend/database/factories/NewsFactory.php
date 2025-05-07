<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'en' => fake()->sentence(),
                'es' => fake()->sentence(),
            ],
            'content' => [
                'en' => fake()->paragraph(1),
                'es' => fake()->paragraph(1),
            ],
            'image_path' => 'news/images/'.$this->faker->uuid().'.jpg',
            'published_at' => fake()->date(),
        ];
    }
}
