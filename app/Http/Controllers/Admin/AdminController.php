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

    // Admin Dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // ===========================
    // MODERATOR MANAGEMENT
    // ===========================

    public function moderators()
    {
        $moderators = User::role('moderator')->with('roles')->paginate(10);
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

        // Check for existing moderator in the same district
        $existing = User::role('moderator')
            ->where('district', $validated['district'])
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'A moderator for this district already exists.');
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'district' => $validated['district'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('moderator');

        return redirect()->route('admin.moderators.index')
            ->with('success', 'Moderator created successfully');
    }

    public function editModerator(User $moderator)
    {
        if ($moderator->hasRole('admin')) {
            abort(403, 'Cannot edit administrator accounts');
        }

        return view('admin.moderators.edit', compact('moderator'));
    }

    public function updateModerator(Request $request, User $moderator)
    {
        if ($moderator->hasRole('admin')) {
            abort(403, 'Cannot update administrator accounts');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($moderator->id)],
            'phone'    => 'required|string|max:15',
            'district' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Check for other moderator in the same district (excluding self)
        $conflict = User::role('moderator')
            ->where('district', $validated['district'])
            ->where('id', '!=', $moderator->id)
            ->first();

        if ($conflict) {
            return redirect()->back()->withInput()->with('error', 'Another moderator already exists for this district.');
        }

        $moderator->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'district' => $validated['district'],
            'password' => $validated['password']
                ? Hash::make($validated['password'])
                : $moderator->password,
        ]);

        return redirect()->route('admin.moderators.index')
            ->with('success', 'Moderator updated successfully');
    }

    public function destroyModerator(User $moderator)
    {
        if ($moderator->hasRole('admin')) {
            abort(403, 'Cannot delete administrator accounts');
        }

        $moderator->delete();

        return redirect()->route('admin.moderators.index')
            ->with('success', 'Moderator deleted successfully');
    }

    // ===========================
    // GENERAL USER MANAGEMENT
    // ===========================

    public function indexUsers()
    {
        $users = User::with('roles')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function editUser(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot edit your own admin account');
        }

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot modify your own account');
        }

        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User role updated successfully');
    }
}
