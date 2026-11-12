<?php

namespace Database\Factories\Users;

use App\Models\Users\Address;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address_label' => fake()->optional()->words(2, true),
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->phoneNumber(),
            'address_line' => fake()->streetAddress(),
            'district' => fake()->optional()->citySuffix(),
            'city' => fake()->city(),
            'province' => fake()->optional()->state(),
            'postal_code' => fake()->postcode(),
            'is_default' => fake()->boolean(20),
        ];
    }
}
