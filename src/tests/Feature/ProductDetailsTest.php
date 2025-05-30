<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * 商品詳細ページで必要な情報が表示されるか
     */
    public function test_product_detail_required_information()
    {
        $user = User::factory()->create();

        $category1 = Category::create(['name' => 'カメラ']);
        $category2 = Category::create(['name' => '電子機器']);

        $product = Product::create([
            'name' => 'デジタルカメラ',
            'brand_name' => 'ブランドC',
            'description' => '高画質なデジタルカメラ',
            'price' => 50000.00,
            'image' => 'camera.jpg',
            'status' => '新商品',
            'user_id' => $user->id,
            'is_sold' => false,
        ]);

        $product->categories()->attach([
            $category1->id => ['user_id' => $user->id],
            $category2->id => ['user_id' => $user->id],
        ]);

        $response = $this->get("/item/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee($product->brand_name);
        $response->assertSee(number_format($product->price));
        $response->assertSee($product->description);
        $response->assertSee($product->image);
        $response->assertSee($product->status);
        $response->assertSee('0');
        $response->assertSee('0');
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
    }

    /**
     * 商品詳細ページで複数選択されたカテゴリが表示されるか
     */
    public function test_multiple_categories_on_product()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $categories = Category::factory(2)->create();

        foreach ($categories as $category) {
            $product->categories()->attach($category->id, ['user_id' => $user->id]);
        }

        $response = $this->get(route('products.show', $product->id));

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}