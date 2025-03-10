<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'manage users',
            'create exams',
            'view exams',
            'edit exams',
            'delete exams',
            'take exams',
            'view results',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $moderator = Role::firstOrCreate(['name' => 'moderator']);
        $paper_seater = Role::firstOrCreate(['name' => 'paper_seater']);
        $student = Role::firstOrCreate(['name' => 'student']);

        // Assign all permissions to Admin
        $admin->givePermissionTo($permissions);

        // Assign relevant permissions to Moderator
        $moderator->givePermissionTo(['create exams', 'view exams', 'edit exams', 'delete exams']);

        // Assign relevant permissions to Paper Setter
        $paper_seater->givePermissionTo(['create exams', 'view exams']);

        // Assign relevant permissions to Student
        $student->givePermissionTo(['take exams', 'view results']);
    }
}
