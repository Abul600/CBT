<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        User::firstOrCreate(
            ['email' => 'test@example.com'], // Check if user exists
            [
                'name' => 'Test User',
                'role' => 'student',
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // Use a secure password in production
                'remember_token' => Str::random(10),
            ]
        );
    }
}
