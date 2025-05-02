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
        $this->middleware(['auth', 'role:moderator'])->except(['activate', 'deactivate']);
        $this->middleware(['auth', 'role:admin'])->only(['activate', 'deactivate']);
    }

    public function dashboard()
    {
        return view('moderator.dashboard');
    }

    public function paperSetters()
    {
        $paperSetters = User::role('paper_setter')->get();
        return view('moderator.paper_setters.index', compact('paperSetters'));
    }

    public function createPaperSetter()
    {
        return view('moderator.paper_setters.create');
    }

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
            'is_active' => true,
        ]);

        $user->assignRole('paper_setter');

        return redirect()->route('moderator.paper_setters.index')->with('success', 'Paper Setter added successfully.');
    }

    public function destroyPaperSetter(User $paperSetter)
    {
        $paperSetter->delete();
        return redirect()->route('moderator.paper_setters.index')->with('success', 'Paper Setter deleted successfully.');
    }

    public function searchQuestions(Request $request)
    {
        return view('moderator.search-questions');
    }

    public function index()
    {
        $moderators = User::role('moderator')->get();
        return view('admin.moderators.index', compact('moderators'));
    }

    public function create()
    {
        return view('admin.moderators.create');
    }

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
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.moderators.index')->with('success', ucfirst($request->role) . ' added successfully.');
    }

    public function edit(User $moderator)
    {
        return view('admin.moderators.edit', compact('moderator'));
    }

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

    public function destroy(User $moderator)
    {
        $moderator->delete();
        return redirect()->route('admin.moderators.index')->with('success', 'Moderator deleted successfully.');
    }

    public function activate($id)
    {
        $moderator = User::findOrFail($id);
        $moderator->is_active = true;
        $moderator->save();

        return redirect()->back()->with('success', 'Moderator activated successfully.');
    }

    public function deactivate($id)
    {
        $moderator = User::findOrFail($id);
        $moderator->is_active = false;
        $moderator->save();

        return redirect()->back()->with('success', 'Moderator deactivated successfully.');
    }
}
