<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    // Show list of moderators
    public function moderators()
    {
        $moderators = User::role('moderator')->get(); // Fetch users with the 'moderator' role
        return view('admin.moderators.index', compact('moderators'));
    }

    // Show the form for creating a new moderator
    public function createModerator()
    {
        return view('admin.moderators.create');
    }

    // Store a newly created moderator
    public function storeModerator(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign 'moderator' role using Spatie
        $user->assignRole('moderator');

        // Debugging: Log the role assignment
        \Log::info('Moderator created:', ['id' => $user->id, 'roles' => $user->getRoleNames()]);

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator added successfully');
    }

    // Show the form for editing a moderator
    public function editModerator(User $moderator)
    {
        return view('admin.moderators.edit', compact('moderator'));
    }

    // Update moderator details
    public function updateModerator(Request $request, User $moderator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $moderator->id,
        ]);

        $moderator->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator updated successfully');
    }

    // Delete a moderator
    public function destroyModerator(User $moderator)
    {
        $moderator->delete();
        return redirect()->route('admin.moderators.index')->with('success', 'Moderator deleted successfully');
    }
}
