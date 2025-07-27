<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'role' => $this->faker->randomElement(['Cashier', 'Stocker', 'Cleaner']),
            'location' => $this->faker->city(),
            'assigned_to' => null,
        ];
    }
}
