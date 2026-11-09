<?php

namespace Database\Factories;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technician>
 */
class TechnicianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'technician']),
            'name' => $this->faker->name(),
            'phone' => '08'.$this->faker->numerify('##########'),
            'is_active' => true,
        ];
    }
}
