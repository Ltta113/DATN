<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'cover_image' => $this->faker->imageUrl(640, 480, 'books'),
            'stock' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->numberBetween(10000, 500000),
        ];
    }
}
