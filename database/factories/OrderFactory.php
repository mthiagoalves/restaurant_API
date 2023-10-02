<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paid = $this->faker->boolean();
        return [
            'user_id' => User::all()->random()->id,
            'table_id' => Table::all()->random()->id,
            'status' => $this->faker->randomElement(['OP', 'RC', 'IP', 'ID', 'CP']),
        ];
    }
}
