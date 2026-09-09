<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
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
