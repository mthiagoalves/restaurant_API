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
            'payment_type' => $this->faker->randomElement(['B', 'CC', 'CD', 'P']),
            'paid' => $paid,
            'value' => $this->faker->numberBetween(1000, 10000),
            'payment_date' => $paid ? $this->faker->randomElement([$this->faker->dateTimeThisMonth()]) : NULL
        ];
    }
}
