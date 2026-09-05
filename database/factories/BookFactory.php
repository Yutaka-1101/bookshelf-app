<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
            'user_id' => User::factory(),
            'author' => $this->faker->name(),
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->numerify('123##########'),
            'published_date' => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'image_url' => $this->faker->imageUrl(),
        ];
    }
}
