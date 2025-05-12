<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 共通のセットアップ処理（CSRFミドルウェアの無効化）
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /**
     * メールアドレスが未入力の場合、バリデーションエラーが表示されるかをテストする
     */
    public function test_email_required()
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが未入力の場合、バリデーションエラーが表示されるかをテストする
     */
    public function test_password_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * 間違った認証情報を入力した場合、バリデーションエラーが表示されるかをテストする
     */
    public function test_incorrect_credential()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * 正しい認証情報を入力した場合、ログイン処理が成功するかをテストする
     */
    public function test_successful_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/mypage');
        $this->assertAuthenticatedAs($user);
    }
}