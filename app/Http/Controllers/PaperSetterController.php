<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PaperSetterController extends Controller
{
    public function index()
    {
        $paperSetters = User::where('role', 'paper_setter')->get();
        return view('moderator.paper_setters.index', compact('paperSetters'));
    }

    public function create()
    {
        return view('moderator.paper_setters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'paper_setter',
        ]);

        return redirect()->route('moderator.paper_setters.index')->with('success', 'Paper Setter created successfully');
    }

    public function destroy(User $paper_setter)
    {
        $paper_setter->delete();
        return redirect()->route('moderator.paper_setters.index')->with('success', 'Paper Setter deleted successfully');
    }

    public function dashboard()
    {
        return view('paper_setter.dashboard');
    }
}
