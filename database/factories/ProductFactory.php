<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'slug' => fake()->slug,
            'category_id' => Category::all()->random()->id,
            'description' => fake()->paragraph,
            'price' => fake()->randomFloat(2, 2, 500),
            'order' => fake()->randomNumber(1, 20)
        ];
    }
}
