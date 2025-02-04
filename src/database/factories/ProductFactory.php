<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'brand_name' => $this->faker->company,
            'description' => $this->faker->text,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'status' => $this->faker->randomElement(['available', 'sold']),
            'image' => $this->faker->imageUrl,
            'user_id' => User::factory(),
        ];
    }
}
