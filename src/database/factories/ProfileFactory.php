<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Profile;
use App\Models\User;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'zipcode' => $this->faker->postcode(),
            'address' => $this->faker->address(),
            'building' => $this->faker->buildingNumber(),
            'profile_picture' => 'storage/default-profile.jpg',
        ];
    }
}
