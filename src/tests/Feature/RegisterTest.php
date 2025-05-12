<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    /**
     * テスト用の登録データを準備する
     */
    private function getRegistrationData($overrides = [])
    {
        return array_merge([
            'name' => 'Test Taro',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    /**
     * バリデーションエラーが適切に表示されることを確認する
     */
    private function assertValidationError($data, $field)
    {
        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors($field);
    }

    /**
     * 名前が未入力の場合、バリデーションメッセージが表示されることを確認する
     */
    public function test_name_required()
    {
        $this->assertValidationError(
            $this->getRegistrationData(['name' => null]),
            'name'
        );
    }

    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示されることを確認する
     */
    public function test_email_required()
    {
        $this->assertValidationError(
            $this->getRegistrationData(['email' => null]),
            'email'
        );
    }

    /**
     * パスワードが7文字以内の場合、バリデーションメッセージが表示されることを確認する
     */
    public function test_password_sevenCharacters()
    {
        $this->assertValidationError(
            $this->getRegistrationData(['password' => 'short', 'password_confirmation' => 'short']),
            'password'
        );
    }

    /**
     * パスワード確認が一致しない場合、バリデーションメッセージが表示されることを確認する
     */
    public function test_password_confirmation()
    {
        $this->assertValidationError(
            $this->getRegistrationData(['password_confirmation' => 'password321']),
            'password'
        );
    }

    /**
     * 全項目が正しく入力された場合、会員情報が登録され、メール認証画面に遷移することを確認する
     */
    public function test_successful_registration()
    {
        $this->withoutMiddleware(EnsureEmailIsVerified::class);

        $response = $this->post('/register', $this->getRegistrationData());
        $response->assertRedirect('/email/verify');
    }
}