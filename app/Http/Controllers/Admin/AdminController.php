<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // Dashboard - No changes needed
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Moderator Management
    public function moderators()
    {
        $moderators = User::role('moderator')->with('roles')->get();
        return view('admin.moderators.index', compact('moderators'));
    }

    public function createModerator()
    {
        return view('admin.moderators.create');
    }

    public function storeModerator(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:15',
            'district' => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'district' => $validated['district'],
            'password' => Hash::make($validated['password']),
            'role'     => 'moderator' // Explicit role assignment
        ]);

        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $user->assignRole($moderatorRole);

        return redirect()->route('admin.moderators.index')
            ->with('success', 'Moderator created successfully');
    }

    public function editModerator(User $moderator)
    {
        // Prevent editing admins
        if ($moderator->hasRole('admin')) {
            abort(403, 'Cannot edit administrator accounts');
        }

        return view('admin.moderators.edit', compact('moderator'));
    }

    public function updateModerator(Request $request, User $moderator)
    {
        // Prevent updating admins
        if ($moderator->hasRole('admin')) {
            abort(403, 'Cannot update administrator accounts');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($moderator->id)],
            'phone'    => 'required|string|max:15',
            'district' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed'
        ]);

        $updateData = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'district' => $validated['district']
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $moderator->update($updateData);

        return redirect()->route('admin.moderators.index')
            ->with('success', 'Moderator updated successfully');
    }

    public function destroyModerator(User $moderator)
    {
        // Prevent deleting admins
        if ($moderator->hasRole('admin')) {
            abort(403, 'Cannot delete administrator accounts');
        }

        $moderator->delete();
        return redirect()->route('admin.moderators.index')
            ->with('success', 'Moderator deleted successfully');
    }

    // User Management
    public function indexUsers()
    {
        $users = User::with('roles')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function editUser(User $user)
    {
        // Prevent editing current admin
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot edit your own admin account');
        }

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        // Prevent self-modification
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot modify your own account');
        }

        $validated = $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user->syncRoles([$validated['role']]);
        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User role updated successfully');
    }
}