<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        $moderators = User::where('role', 'moderator')->get();
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'moderator', // Ensure correct role assignment
        ]);

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $moderator->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

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
