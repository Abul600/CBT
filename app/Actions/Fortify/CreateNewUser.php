<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        // ✅ Validate User Input
        $validated = Validator::make($input, [
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

            // ✅ Ensure "Student" Role Exists (Case-Sensitive Fix)
            $studentRole = Role::where('name', 'Student')->first();

            if ($studentRole) {
                $user->assignRole($studentRole); // ✅ Assign correct role with correct capitalization
            } else {
                throw new \Exception("❌ Role 'Student' does not exist.");
            }

            return $user;
        });
    }
}
