<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'buyer_id' => \App\Models\User::factory(),
            'product_id' => \App\Models\Product::factory(),
            'status' => 'in_progress',
        ];
    }
}
