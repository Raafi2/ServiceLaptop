<?php

namespace Database\Factories;

use App\Enums\ServiceStatus;
use App\Helpers\TrackingNumber;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracking_no' => TrackingNumber::generate(),
            'customer_id' => Customer::factory(),
            'technician_id' => Technician::factory(),
            'laptop' => $this->faker->word().' '.$this->faker->word(),
            'complaint' => $this->faker->sentence(),
            'agreed_cost' => $this->faker->numberBetween(100000, 2000000),
            'photo_path' => 'services/dummy.jpg',
            'thumb_path' => 'services/thumb_dummy.jpg',
            'status' => ServiceStatus::Dikerjakan,
            'received_by' => User::factory(),
            'received_at' => now(),
        ];
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ServiceStatus::Selesai,
            'finished_by' => User::factory(),
            'finished_at' => now(),
        ]);
    }

    public function serahkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ServiceStatus::Serahkan,
            'finished_by' => User::factory(),
            'finished_at' => now(),
            'handed_over_by' => User::factory(),
            'handed_over_at' => now(),
        ]);
    }

    public function diambil(): static
    {
        return $this->serahkan();
    }

    public function dibatalkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ServiceStatus::Dibatalkan,
            'cancelled_by' => User::factory(),
            'cancelled_at' => now(),
            'cancel_reason' => 'Test cancellation',
        ]);
    }
}
