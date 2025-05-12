<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザーとプロフィールを作成する
     */
    protected function createUserProfile(array $userData, array $profileData)
    {
        $user = User::factory()->create($userData);
        $profile = Profile::factory()->create(array_merge($profileData, ['user_id' => $user->id]));

        return $profile;
    }

    /**
     * プロフィール編集ページで各項目の初期値が表示されているか
     */
    public function test_profile_edit_page()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        $profileData = [
            'name' => 'John Doe',
            'zipcode' => '123-4567',
            'address' => '東京都千代田区1-1-1',
            'profile_picture' => 'storage/default-profile.jpg',
        ];

        $profile = $this->createUserProfile($userData, $profileData);

        $this->actingAs($profile->user);

        $response = $this->get(route('profile.edit'));

        $response->assertSee(asset('storage/' . $profile->profile_picture));

        $response->assertSee($profile->name);
        $response->assertSee($profile->zipcode);
        $response->assertSee($profile->address);
    }
}