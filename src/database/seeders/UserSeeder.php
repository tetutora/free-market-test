<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $user3 = User::create([
            'name' => 'ユーザー3',
            'email' => 'user3@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id' => $user1->id,
            'profile_picture' => null,
            'name' => 'ユーザー1',
            'zipcode' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => 'ユーザー1ビル',
        ]);

        Profile::create([
            'user_id' => $user2->id,
            'profile_picture' => null,
            'name' => 'ユーザー2',
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1',
            'building' => 'ユーザー2タワー',
        ]);

        Profile::create([
            'user_id' => $user3->id,
            'profile_picture' => null,
            'name' => 'ユーザー3',
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1',
            'building' => 'ユーザー3タワー',
        ]);
    }
}
