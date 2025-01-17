<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        User::create([
            'name' => 'Default User',
            'email' => 'default@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'email_verification_hash' => bcrypt('default@example.com'),
        ]);
    }
}
