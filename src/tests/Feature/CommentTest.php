<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    /**
     * ログイン済みのユーザーがコメントを投稿できるかをテストする
     */
    public function test_login_user_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->followingRedirects()
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

    /**
     * ログインしていないユーザーはコメントを投稿できないことをテストする
     */
    public function test_not_login_user_cannot_comment()
    {
        $product = Product::factory()->create();

        $response = $this->postJson("/products/{$product->id}/add-comment", [
                        'content' => 'This is a test comment.'
                    ]);

        $response->assertRedirect("/item/{$product->id}");

        $this->assertDatabaseMissing('comments', [
            'content' => 'This is a test comment.'
        ]);
    }

    /**
     * コメントが未入力の場合、バリデーションエラーが発生するかをテストする
     */
    public function test_comment_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->postJson("/products/{$product->id}/add-comment", [
                        'content' => ''
                    ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }

    /**
     * コメントが255文字を超える場合にバリデーションエラーが発生するかをテストする
     */
    public function test_comment_255_characters()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $longComment = str_repeat('a', 256);

        $response = $this->postJson(
            "/products/{$product->id}/add-comment", ['content' => $longComment],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }
}