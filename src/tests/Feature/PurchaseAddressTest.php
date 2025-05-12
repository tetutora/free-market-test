<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー、プロフィール、商品、購入を作成する
     */
    protected function createUserProductProfileData(array $userData, array $profileData)
    {
        $user = User::factory()->create($userData);

        $profile = Profile::create(array_merge($profileData, ['user_id' => $user->id]));
        $product = Product::factory()->create(['user_id' => $user->id]);

        return compact('user', 'profile', 'product');
    }

    /**
     * 送付先住所変更が商品購入画面に反映されるか
     */
    public function test_address_is_reflected_on_purchase_page()
    {
        $data = $this->createUserProductProfileData(
            ['name' => 'テストユーザー'],
            [
                'name' => 'テストユーザー',
                'zipcode' => '123-4567',
                'address' => '東京都渋谷区',
                'building' => '青山マンション101号',
            ]
        );

        $this->actingAs($data['user']);

        $response = $this->get(route('purchase.show', ['item_id' => $data['product']->id]));

        $response->assertSee('〒 123-4567');
        $response->assertSee('東京都渋谷区 青山マンション101号');
    }

    /**
     * 購入した商品がユーザーに紐づくか
     */
    public function test_purchased_item_is_linked_to_user()
    {
        $data = $this->createUserProductProfileData(
            ['name' => 'テストユーザー'],
            [
                'name' => 'テストユーザー',
                'zipcode' => '123-4567',
                'address' => '東京都渋谷区',
                'building' => '青山マンション101号',
            ]
        );

        $this->actingAs($data['user']);

        $purchase = Purchase::factory()->create([
            'user_id' => $data['user']->id,
            'product_id' => $data['product']->id,
            'seller_id' => $data['product']->user_id,
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $data['user']->id,
            'product_id' => $data['product']->id,
        ]);
    }
}