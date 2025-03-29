<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class PaperSetterController extends Controller
{
    /**
     * Display a listing of the paper setters.
     */
    public function index()
    {
        if (!Gate::allows('manage-paper-setters')) {
            abort(403);
        }

        $paperSetters = User::role('paper_setter')->get(); // Get all Paper Setters
        return view('moderator.paper_setters.index', compact('paperSetters'));
    }

    /**
     * Show the form for creating a new paper setter.
     */
    public function create()
    {
        return view('moderator.paper_setters.create');
    }

    /**
     * Store a newly created paper setter in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-paper-setters')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => 1, // Default active
        ]);

        $user->assignRole('paper_setter');

        return redirect()->route('moderator.paper_setters.index')
                         ->with('success', 'Paper Setter added successfully.');
    }

    /**
     * Show the form for editing the specified paper setter.
     */
    public function edit(User $paperSetter) // Using route model binding
    {
        return view('moderator.paper_setters.edit', compact('paperSetter'));
    }

    /**
     * Update the specified paper setter in storage.
     */
    public function update(Request $request, User $paperSetter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $paperSetter->id,
        ]);

        $paperSetter->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('moderator.paper_setters.index')
                         ->with('success', 'Paper Setter updated successfully.');
    }

    /**
     * Remove the specified paper setter from storage.
     */
    public function destroy(User $paperSetter)
    {
        $paperSetter->delete();

        return redirect()->route('moderator.paper_setters.index')
                         ->with('success', 'Paper Setter deleted successfully.');
    }

    /**
     * Toggle activation status of a paper setter.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active; // Toggle status
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully!');
    }

    /**
     * Load the dashboard for paper setters.
     */
    public function dashboard()
    {
        return view('paper_setter.dashboard'); // Ensure this view exists
    }

    /**
     * Display a listing of the questions for Paper Setters.
     */
    public function questionIndex()
    {
        return view('paper_setter.questions.index'); // Ensure this view exists
    }

    /**
     * Show form to create a new question.
     */
    public function createQuestion()
    {
        return view('paper_setter.questions.create');
    }

    /**
     * Store a newly created question.
     */
    public function storeQuestion(Request $request)
    {
        // Implement question storing logic here
        return redirect()->route('paper_setter.questions.index')
                         ->with('success', 'Question added successfully.');
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion($id)
    {
        // Implement question deletion logic here
        return redirect()->route('paper_setter.questions.index')
                         ->with('success', 'Question deleted successfully.');
    }
}
