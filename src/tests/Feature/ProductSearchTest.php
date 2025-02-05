<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    // 商品名で部分一致検索ができるか
    public function test_product_name_search()
    {
        $user = User::factory()->create();

        Product::create([
            'name' => 'デジタルカメラ',
            'brand_name' => 'ブランドA',
            'description' => '高画質なデジタルカメラ',
            'price' => 50000.00,
            'image' => 'camera.jpg',
            'status' => '新商品',
            'user_id' => $user->id,
            'is_sold' => false,
        ]);

        $response = $this->get('/?search=デジタルカメラ');

        $response->assertStatus(200);
        $response->assertSee('デジタルカメラ');
    }

    // 検索状態がマイリストでも保持されているか
    public function test_product_name_search_with_mylist()
    {
        $user = User::factory()->create();

        Product::create([
            'name' => 'デジタルカメラ',
            'brand_name' => 'ブランドA',
            'description' => '高画質なデジタルカメラ',
            'price' => 50000.00,
            'image' => 'camera.jpg',
            'status' => '新商品',
            'user_id' => $user->id,
            'is_sold' => false,
        ]);

        $this->get('/?search=デジタルカメラ');

        $response = $this->get('/?page=mylist&search=デジタルカメラ');

        $response->assertStatus(200);
        $response->assertSee('デジタルカメラ');
    }
}
