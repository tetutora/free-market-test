<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    // 支払い方法の選択が反映されるか
    public function test_payment_method()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 5000,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('purchase.show', ['item_id' => $product->id]));
        $response->assertStatus(200);
        $response->assertSee('カード払い');

        $response = $this->withSession(['payment_method' => 'bank_transfer'])
            ->post(route('products.purchase', ['item_id' => $product->id]), [
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertStatus(302);

        $response->assertSessionHas('payment_method', 'bank_transfer');
    }
}

