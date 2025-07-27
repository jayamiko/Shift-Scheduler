<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;

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
        $jakartaLocations = [
            'Kemang', 'Kuningan', 'Cilandak', 'Tebet', 'Sudirman',
            'Kelapa Gading', 'Tanah Abang', 'Pondok Indah', 'Senayan', 'Menteng'
        ];

        return [
            'date' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'role_id' => Role::inRandomOrder()->first()?->id,
            'location' => $this->faker->randomElement($jakartaLocations),
            'assigned_to' => null,
        ];
    }
}
