<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー、プロフィール、商品、購入を作成する
     */
    protected function createUserProfileData(array $userData, array $profileData)
    {
        $user = User::factory()->create($userData);

        $profile = Profile::factory()->create(array_merge($profileData, ['user_id' => $user->id]));

        $product1 = Product::factory()->create(['user_id' => $user->id]);
        $product2 = Product::factory()->create(['user_id' => $user->id]);

        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'seller_id' => $product1->user_id,
        ]);

        return compact('user', 'profile', 'product1', 'product2', 'purchase');
    }

    /**
     * マイページで必要な情報が取得できているか
     */
    public function test_profile_page()
    {
        $data = $this->createUserProfileData(
            ['name' => 'Fiona Murray'],
            ['name' => 'Fiona Murray']
        );

        $this->actingAs($data['user']);

        $response = $this->get(route('profile.mypage'));

        $response->assertSee(asset('storage/' . $data['profile']->profile_picture));
        $response->assertSee('Fiona Murray');
        $response->assertSee($data['product1']->name);
        $response->assertSee($data['product2']->name);
        $response->assertSee($data['purchase']->product->name);
    }
}