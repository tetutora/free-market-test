<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // ログイン済みのユーザーがコメントと投稿できるか
    public function test_login_user_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                    ->followingRedirects()
                    ->postJson("/products/{$product->id}/add-comment", [
                        'content' => 'This is a test comment.'
                    ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment.',
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    // ログイン前のユーザーはコメントできないようになっているか
    public function test_not_login_user_cannot_comment()
    {
        $product = Product::factory()->create();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                    ->postJson("/products/{$product->id}/add-comment", [
                        'content' => 'This is a test comment.'
                    ]);

        $response->assertRedirect("/item/{$product->id}");

        $this->assertDatabaseMissing('comments', [
            'content' => 'This is a test comment.'
        ]);
    }

    // コメントが未入力の場合、バリデーションメッセージが表示されるか
    public function test_comment_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                    ->postJson("/products/{$product->id}/add-comment", [
                        'content' => ''
                    ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }

    // コメントが255文字を超える場合mバリデーションメッセージが表示されるか
    public function test_comment_255_characters()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $longComment = str_repeat('a',256);

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                    ->postJson("/products/{$product->id}/add-comment", [
                        'content' => $longComment
                    ],[
                        'Accept' => 'application/json'
                    ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }
}
