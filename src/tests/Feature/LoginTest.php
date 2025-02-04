<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // メールアドレスが入力されていない場合、バリデーションメッセージが表示されるか
    public function testEmailIsRequired()
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示されるか
    public function testPasswordIsRequired()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);
        $response->assertSessionHasErrors('password');
    }

    // 入力情報が間違っている場合、バリデーションメッセージが表示されるか
    public function testIncorrectCredential()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors('email');
    }

    // 正しい情報が入力された場合、ログイン処理が実行されるか
    public function testSuccessfulLogin(){
        $user = User::factory()->create([
            'email' => 'test@example',
            'password' => Hash::make('password123'),
        ]);
        
        $this->actingAs($user);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
