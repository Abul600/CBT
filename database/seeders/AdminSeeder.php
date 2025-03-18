<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch the 'Admin' role from the roles table
        $adminRole = Role::where('name', 'Admin')->first();

        if (!$adminRole) {
            echo "❌ Error: 'Admin' role not found. Run RoleSeeder first.\n";
            return;
        }

        // Ensure admin user exists or create a new one
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // Ensure uniqueness
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'), // Secure password hashing
                'role_id' => $adminRole->id, // Assign role from database
            ]
        );

        echo "✅ Admin user seeded successfully.\n";
    }
}
