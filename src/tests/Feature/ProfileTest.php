<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Purchase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // マイページで必要な情報が取得できているか
    public function test_profile_page()
    {
        $user = User::factory()->create(['name' => 'Fiona Murray']);
        $profile = Profile::factory()->create(['user_id' => $user->id, 'name' => 'Fiona Murray']);
        $product1 = Product::factory()->create(['user_id' => $user->id]);
        $product2 = Product::factory()->create(['user_id' => $user->id]);
        $purchase = Purchase::factory()->create(['user_id' => $user->id, 'product_id' => $product1->id]);

        $this->actingAs($user);

        $response = $this->get(route('profile.mypage'));

        $response->assertSee(asset('storage/' . $profile->profile_picture));

        $response->assertSee('Fiona Murray');

        $response->assertSee($product1->name);
        $response->assertSee($product2->name);

        $response->assertSee($purchase->product->name);
    }
}
