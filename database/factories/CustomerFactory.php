<?php

namespace Database\Factories;

use App\Helpers\CustomerKey;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        $phone = '08'.$this->faker->numerify('##########');

        return [
            'name' => $name,
            'name_key' => CustomerKey::normalizeName($name),
            'phone' => CustomerKey::normalizePhone($phone),
        ];
    }
}
