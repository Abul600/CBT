<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Rules\UserCanBeModerator;
use App\Rules\DistrictAvailableForModeratorAssignment;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function moderators()
    {
        $moderators = User::role('moderator')->with('roles')->paginate(10);
        return view('admin.moderators.index', compact('moderators'));
    }

    public function createModerator()
    {
        $districts = District::all(); // Fetch all districts
        return view('admin.moderators.create', compact('districts'));
    }

    public function storeModerator(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|max:15',
            'district_id' => 'required|exists:districts,id',
            'password'    => 'required|min:8|confirmed',
        ]);

        // Check for existing moderator in the same district
        $existing = User::role('moderator')
            ->where('district_id', $validated['district_id'])
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'A moderator for this district already exists.');
        }

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'district_id' => $validated['district_id'],
            'password'    => Hash::make($validated['password']),
            'is_moderator' => true,
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
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'email', Rule::unique('users')->ignore($moderator->id)],
            'phone'       => 'required|string|max:15',
            'district_id' => 'required|exists:districts,id',
            'password'    => 'nullable|min:8|confirmed',
        ]);

        // Check for another moderator in the same district
        $conflict = User::role('moderator')
            ->where('district_id', $validated['district_id'])
            ->where('id', '!=', $moderator->id)
            ->first();

        if ($conflict) {
            return redirect()->back()->withInput()->with('error', 'Another moderator already exists for this district.');
        }

        $moderator->update([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'district_id' => $validated['district_id'],
            'password'    => $validated['password']
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

    public function assignModerator(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                new UserCanBeModerator
            ],
            'district_id' => [
                'required',
                'exists:districts,id',
                new DistrictAvailableForModeratorAssignment
            ]
        ]);

        try {
            User::findOrFail($validated['user_id'])->update([
                'district_id' => $validated['district_id'],
                'is_moderator' => true
            ]);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'users_active_moderator_district_unique')) {
                return back()->withErrors([
                    'district_id' => 'This district already has an active moderator'
                ]);
            }
            throw $e;
        }

        return redirect()->route('admin.moderators.index')
                         ->with('success', 'Moderator assigned successfully');
    }

    public function deactivateModerator(User $moderator)
    {
        abort_unless($moderator->is_moderator, 403);

        $moderator->update(['is_moderator' => false]);

        return back()->with('success', 'Moderator deactivated');
    }

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
