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
        // ✅ Ensures only authenticated users with 'Moderator' role can access this controller
        $this->middleware(['auth', 'role:Moderator']);
    }

    // ✅ Moderator Dashboard
    public function dashboard()
    {
        return view('moderator.dashboard');
    }

    // ✅ View Paper Setters
    public function paperSetters()
    {
        $paperSetters = User::role('paper_seater')->get();
        return view('moderator.paper-setters.index', compact('paperSetters'));
    }

    // ✅ Search & Filter Questions (To build exams)
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

    // ✅ Store a newly created moderator in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:15',
            'district' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        $moderator = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'district' => $request->district,
            'password' => Hash::make($request->password),
            'role' => 'moderator',
        ]);

        $moderator->assignRole('moderator');

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator created successfully.');
    }

    // ✅ Show the form for editing a moderator
    public function edit(User $moderator)
    {
        return view('admin.moderators.edit', compact('moderator'));
    }

    // ✅ Update the moderator's details in the database
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
}
