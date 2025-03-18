<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder {
    public function run(): void {
        $roles = ['Admin', 'Moderator', 'Paper Seater', 'Student'];

        // Disable foreign key checks (useful for resetting)
        Schema::disableForeignKeyConstraints();

        // Check if using Spatie's package or custom Role model
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $roleClass = \Spatie\Permission\Models\Role::class;
        } else {
            $roleClass = \App\Models\Role::class;
        }

        foreach ($roles as $role) {
            $roleClass::firstOrCreate(['name' => $role]); // ✅ Ensures no duplicate roles
        }

        // Enable foreign key checks
        Schema::enableForeignKeyConstraints();

        echo "✅ Roles seeded successfully.\n";
    }
}