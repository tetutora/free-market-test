<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品を作成する
     */
    protected function createProduct(string $name, string $brand)
    {
        $user = User::factory()->create();

        return Product::create([
            'name' => $name,
            'brand_name' => $brand,
            'description' => '高画質なデジタルカメラ',
            'price' => 50000.00,
            'image' => 'camera.jpg',
            'status' => '新商品',
            'user_id' => $user->id,
            'is_sold' => false,
        ]);
    }

    /**
     * 商品名で部分一致検索ができるか
     */
    public function test_product_name_search()
    {
        $product = $this->createProduct('デジタルカメラ', 'ブランドA');

        $response = $this->get('/?search=デジタルカメラ');

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    /**
     * 検索状態がマイリストでも保持されているか
     */
    public function test_product_name_search_with_mylist()
    {
        $product = $this->createProduct('デジタルカメラ', 'ブランドA');

        $this->get('/?search=デジタルカメラ');
        $response = $this->get('/?page=mylist&search=デジタルカメラ');

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }
}