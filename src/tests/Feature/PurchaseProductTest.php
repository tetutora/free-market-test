<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class PurchaseProductTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // ユーザーが商品を購入できるか
    public function test_purchase_product()
    {
        $user = User::factory()->create();
        $user->email_verified_at = now();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'is_sold' => false
        ]);

        $response = $this->post("/purchase/{$product->id}/success");

        $response->assertStatus(302);
    }
}
