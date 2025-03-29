<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ModeratorController extends Controller
{
    public function __construct()
    {
        // ✅ Ensures only authenticated users with 'moderator' role can access this controller
        $this->middleware(['auth', 'role:moderator'])->except(['activate', 'deactivate']);
        $this->middleware(['auth', 'role:admin'])->only(['activate', 'deactivate']);
    }

    // ✅ Moderator Dashboard
    public function dashboard()
    {
        return view('moderator.dashboard');
    }

    // ✅ View Paper Setters
    public function paperSetters()
    {
        $paperSetters = User::role('paper_setter')->get();
        return view('moderator.paper_setters.index', compact('paperSetters'));
    }

    // ✅ Show form to create a new Paper Setter
    public function createPaperSetter()
    {
        return view('moderator.paper_setters.create');
    }

    // ✅ Store new Paper Setter
    public function storePaperSetter(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'district' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'district' => $request->district,
            'password' => Hash::make($request->password),
            'is_active' => 1, // Paper Setters are active by default
        ]);

        $user->assignRole('paper_setter');

        return redirect()->route('moderator.paper_setters.index')->with('success', 'Paper Setter added successfully.');
    }

    // ✅ Delete a Paper Setter
    public function destroyPaperSetter(User $paperSetter)
    {
        $paperSetter->delete();
        return redirect()->route('moderator.paper_setters.index')->with('success', 'Paper Setter deleted successfully.');
    }

    // ✅ Search & Filter Questions
    public function searchQuestions(Request $request)
    {
        return view('moderator.search-questions');
    }

    // ✅ Display the list of moderators (For Admin Panel)
    public function index()
    {
        $moderators = User::role('moderator')->get();
        return view('admin.moderators.index', compact('moderators'));
    }

    // ✅ Show the form for creating a new moderator
    public function create()
    {
        return view('admin.moderators.create');
    }

    // ✅ Store a newly created user (Moderator, Student, or Paper Setter)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'district' => 'required|string',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:moderator,student,paper_setter',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'district' => $request->district,
            'password' => Hash::make($request->password),
            'is_active' => 1, // New users are active by default
        ]);

        if ($role = Role::where('name', $request->role)->first()) {
            $user->assignRole($role);
        } else {
            return redirect()->back()->with('error', 'The selected role does not exist!');
        }

        return redirect()->route('admin.moderators.index')->with('success', ucfirst($request->role) . ' added successfully');
    }

    // ✅ Show the form for editing a moderator
    public function edit(User $moderator)
    {
        return view('admin.moderators.edit', compact('moderator'));
    }

    // ✅ Update the moderator's details
    public function update(Request $request, User $moderator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $moderator->id,
        ]);

        $moderator->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator updated successfully.');
    }

    // ✅ Delete a moderator
    public function destroy(User $moderator)
    {
        $moderator->delete();
        return redirect()->route('admin.moderators.index')->with('success', 'Moderator deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Moderator Activation & Deactivation (For Admin)
    |--------------------------------------------------------------------------
    */

    // ✅ Activate a Moderator
    public function activate($id)
    {
        $moderator = User::findOrFail($id);
        $moderator->is_active = 1;
        $moderator->save();

        return redirect()->back()->with('success', 'Moderator activated successfully.');
    }

    // ✅ Deactivate a Moderator
    public function deactivate($id)
    {
        $moderator = User::findOrFail($id);
        $moderator->is_active = 0;
        $moderator->save();

        return redirect()->back()->with('success', 'Moderator deactivated successfully.');
    }
}
