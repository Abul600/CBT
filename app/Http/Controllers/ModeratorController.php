<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ModeratorController extends Controller
{
    // Show all moderators
    public function index()
    {
        $moderators = User::where('role', 'moderator')->get();
        return view('admin.moderators.index', compact('moderators'));
    }

    // Show form to create a moderator
    public function create()
    {
        return view('admin.moderators.create');
    }

    // Store a new moderator
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'moderator',
            'remember_token' => Str::random(10),
        ]);

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator added successfully!');
    }

    // Show form to edit a moderator
    public function edit($id)
    {
        $moderator = User::findOrFail($id);
        return view('admin.moderators.edit', compact('moderator'));
    }

    // Update a moderator's details
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $moderator = User::findOrFail($id);
        $moderator->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator updated successfully!');
    }

    // Delete a moderator
    public function destroy($id)
    {
        $moderator = User::findOrFail($id);
        $moderator->delete();

        return redirect()->route('admin.moderators.index')->with('success', 'Moderator deleted successfully!');
    }
}
