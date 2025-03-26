<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Ensure only admins can access this controller.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Admin Dashboard.
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Display a list of all moderators.
     */
    public function moderators()
    {
        $moderators = User::role('moderator')->get();
        return view('admin.moderators.index', compact('moderators'));
    }

    /**
     * Show the form to create a new moderator.
     */
    public function createModerator()
    {
        return view('admin.moderators.create');
    }

    /**
     * Store a newly created moderator.
     */
    public function storeModerator(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:15',
            'district' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'district' => $request->district,
            'password' => Hash::make($request->password),
        ]);

        // Assign "moderator" role
        if ($role = Role::where('name', 'moderator')->first()) {
            $user->assignRole($role);
        } else {
            return redirect()->back()->with('error', 'Moderator role does not exist!');
        }

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator added successfully.');
    }

    /**
     * Show the form to edit a moderator.
     */
    public function editModerator(User $moderator)
    {
        return view('admin.moderators.edit', compact('moderator'));
    }

    /**
     * Update an existing moderator.
     */
    public function updateModerator(Request $request, User $moderator)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $moderator->id,
        ]);

        $moderator->update($request->only('name', 'email'));

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator updated successfully.');
    }

    /**
     * Delete a moderator.
     */
    public function destroyModerator(User $moderator)
    {
        $moderator->delete();
        return redirect()->route('admin.moderators.index')->with('success', 'Moderator deleted successfully.');
    }

    /**
     * Store a newly created user with a selected role.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:15',
            'district' => 'required|string',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'district' => $request->district,
            'password' => Hash::make($request->password),
        ]);

        // Assign selected role
        if ($role = Role::where('name', $request->role)->first()) {
            $user->assignRole($role);
        } else {
            return redirect()->back()->with('error', 'Selected role does not exist!');
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully with role: ' . $request->role);
    }
}
