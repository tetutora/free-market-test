<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();
        Storage::fake('public');
        $image = UploadedFile::fake()->create('product.jpg', 500);

        $response = $this->post(route('products.store'), [
            'image' => $image,
            'category_id' => $category->id,
            'status' => '良好',
            'name' => 'Test Product',
            'brand_name' => 'Test Brand',
            'description' => 'This is a test product.',
            'price' => 1000,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'brand_name' => 'Test Brand',
            'description' => 'This is a test product.',
            'price' => 1000,
            'status' => '良好',
            'user_id' => $user->id,
        ]);
        Storage::disk('public')->assertExists('products/' . $image->hashName());

        $product = Product::where('name', 'Test Product')->first();
        $this->assertTrue($product->categories->contains($category));
    }
}
