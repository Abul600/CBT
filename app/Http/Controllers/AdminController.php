<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Ensure only admins can access this controller
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // Admin Dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Display list of moderators
    public function moderators()
    {
        $moderators = User::role('moderator')->get();
        return view('admin.moderators.index', compact('moderators'));
    }

    // Show the form for creating a new moderator
    public function createModerator()
    {
        return view('admin.moderators.create');
    }

    // Store a newly created moderator (or any user with a chosen role)
    public function storeModerator(Request $request)
    {
        // Validate input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|string|in:admin,moderator,paper setter,student',
        ]);

        // Create the user with the provided role
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role, // Use role from the form
        ]);

        // Use Spatie's syncRoles to assign the role for permission management
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.moderators.index')
            ->with('success', 'User created successfully with role ' . $request->role);
    }

    // Show the form for editing a moderator
    public function editModerator(User $moderator)
    {
        return view('admin.moderators.edit', compact('moderator'));
    }

    // Update the moderator's details
    public function updateModerator(Request $request, User $moderator)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $moderator->id,
            'role'  => 'required|string|in:admin,moderator,paper setter,student',
        ]);

        $moderator->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);

        $moderator->syncRoles([$request->role]);

        return redirect()->route('admin.moderators.index')
            ->with('success', 'User updated successfully.');
    }

    // Delete a moderator
    public function destroyModerator(User $moderator)
    {
        $moderator->delete();
        return redirect()->route('admin.moderators.index')
            ->with('success', 'User deleted successfully.');
    }
}
