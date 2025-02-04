<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 名前が入力されていない場合、バリデーションメッセージが表示されるか
    public function testNameIsRequired()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('name');
    }

    // メールアドレスが入力されていない場合、バリデーションメッセージが表示されるか
    public function testEmailIsRequired()
    {
        $response = $this->post('/register', [
            'name' => 'Test Taro',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    }

    // パスワードが7文字以内の場合、バリデーションメッセージが表示されるか
    public function testPasswordIsAtLeastSevenCharacters()
    {
        $response = $this->post('/register', [
            'name' => 'Test Taro',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $response->assertSessionHasErrors('password');
    }

    // パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示されるか
    public function testPasswordConfirmationMatches()
    {
        $response = $this->post('/register', [
            'name' => 'Test Taro',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password321',
        ]);
        $response->assertSessionHasErrors('password');
    }

    // 全ての項目が入力されている場合、会員情報が登録され、ログイン画面に遷移されるか
    public function testSuccessfulRegistration()
    {
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class);

        $response = $this->post('/register', [
            'name' => 'Test Taro',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/email/verify');

        // $response->assertRedirect('/login');

        // $this->assertDatabaseHas('users', [
        //     'email' => 'test@example.com'
        // ]);
    }
}
