<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PaperSeaterController extends Controller
{
    public function index()
    {
        $paperSeaters = User::where('role', 'paper_seater')->get();
        return view('moderator.paper_seaters.index', compact('paperSeaters'));
    }

    public function create()
    {
        return view('moderator.paper_seaters.create');
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
            'role' => 'paper_seater',
        ]);

        return redirect()->route('moderator.paper_seaters.index')->with('success', 'Paper Seater created successfully');
    }

    public function destroy(User $paper_seater)
    {
        $paper_seater->delete();
        return redirect()->route('moderator.paper_seaters.index')->with('success', 'Paper Seater deleted successfully');
    }

    public function dashboard()
    {
        return view('paper_seater.dashboard');
    }
}
