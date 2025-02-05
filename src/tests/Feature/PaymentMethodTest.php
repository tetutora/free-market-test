<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Stripe\Stripe;

class PaymentMethodTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    public function test_payment_method()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = product::factory()->create([
            'price' => 5000,
        ]);

        $response = $this->postJson(route('stripe.checkout'),[
            'payment_method' => 'credit_card',
            'item_id' => $product->id,
        ]);

        $response->assertStatus(200)->assertJsonStructure(['id']);
    }
}
