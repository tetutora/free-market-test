<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    // プロフィール編集ページで各項目の初期値が表示されているか
    public function test_profile_edit_page_()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);

        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'zipcode' => '123-4567',
            'address' => '東京都千代田区1-1-1',
            'profile_picture' => 'storage/default-profile.jpg',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));

        $response->assertSee(asset('storage/' . $profile->profile_picture));

        $response->assertSee($profile->name);

        $response->assertSee($profile->zipcode);

        $response->assertSee($profile->address);
    }
}
