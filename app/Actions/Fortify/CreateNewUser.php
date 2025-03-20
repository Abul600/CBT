<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
<<<<<<< HEAD
use Spatie\Permission\Models\Role;
=======
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
>>>>>>> ab83f84 (minor changes)

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
<<<<<<< HEAD
        // ✅ Validate User Input
        $validated = Validator::make($input, [
=======
        // Validate user input
        Validator::make($input, [
>>>>>>> ab83f84 (minor changes)
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ])->validate();

        return DB::transaction(function () use ($validated) {
            // ✅ Create User Securely
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), // ✅ Always hash passwords
            ]);

<<<<<<< HEAD
            // ✅ Ensure "Student" Role Exists (Case-Sensitive Fix)
            $studentRole = Role::where('name', 'Student')->first();

            if ($studentRole) {
                $user->assignRole($studentRole); // ✅ Assign correct role with correct capitalization
            } else {
                throw new \Exception("❌ Role 'Student' does not exist.");
            }
=======
            // Ensure the "student" role exists before assigning
            $role = Role::firstOrCreate(['name' => 'student']);
            $user->assignRole($role);
>>>>>>> ab83f84 (minor changes)

            return $user;
        });
    }
}
