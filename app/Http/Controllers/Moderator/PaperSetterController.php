<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class PaperSetterController extends Controller
{
    /** Display a listing of the paper setters created by this moderator. */
    public function index()
    {
        if (!Gate::allows('manage-paper-setters')) {
            abort(403);
        }

        $paperSetters = User::role('paper_setter')
            ->where('moderator_id', auth()->id())
            ->get();

        return view('moderator.paper_setters.index', compact('paperSetters'));
    }

    /** Show the form for creating a new paper setter. */
    public function create()
    {
        $activeCount = User::role('paper_setter')
            ->where('moderator_id', auth()->id())
            ->where('is_active', 1)
            ->count();

        if ($activeCount >= 3) {
            return redirect()->route('moderator.paper_setters.index')
                ->with('error', 'You can only have 3 active paper setters at a time. Deactivate one to add a new.');
        }

        return view('moderator.paper_setters.create');
    }

    /** Store a newly created paper setter in storage. */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-paper-setters')) {
            abort(403);
        }

        $activeCount = User::role('paper_setter')
            ->where('moderator_id', auth()->id())
            ->where('is_active', 1)
            ->count();

        if ($activeCount >= 3) {
            return redirect()->route('moderator.paper_setters.index')
                ->with('error', 'Cannot add more than 3 active paper setters. Please deactivate one first.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Get the logged-in moderator
        $moderator = auth()->user();

        // Create the user with auto-assigned moderator_id and district
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'district' => $moderator->district,
            'moderator_id' => $moderator->id,
            'is_active' => 1,
        ]);

        $user->assignRole('paper_setter');

        return redirect()->route('moderator.paper_setters.index')
            ->with('success', 'Paper Setter added successfully.');
    }

    /** Show the form for editing the specified paper setter. */
    public function edit(User $paperSetter)
    {
        if ($paperSetter->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('moderator.paper_setters.edit', compact('paperSetter'));
    }

    /** Update the specified paper setter in storage. */
    public function update(Request $request, User $paperSetter)
    {
        if ($paperSetter->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

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

    /** Remove the specified paper setter from storage. */
    public function destroyPaperSetter(User $paperSetter)
    {
        if ($paperSetter->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $paperSetter->delete();

        return redirect()->route('moderator.paper_setters.index')
            ->with('success', 'Paper Setter deleted successfully.');
    }

    /** Toggle activation status of a paper setter. */
    public function toggleStatus($id)
    {
        $user = User::role('paper_setter')
            ->where('id', $id)
            ->where('moderator_id', auth()->id())
            ->firstOrFail();

        if (!$user->is_active) {
            $activeCount = User::role('paper_setter')
                ->where('moderator_id', auth()->id())
                ->where('is_active', 1)
                ->count();

            if ($activeCount >= 3) {
                return redirect()->back()->with('error', 'Cannot activate more than 3 paper setters.');
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully!');
    }

    /** Paper Setter Dashboard. */
    public function dashboard()
    {
        return view('paper_setter.dashboard');
    }

    /** Display a listing of questions created by the logged-in paper setter. */
    public function questionIndex()
    {
        $questions = Question::where('created_by', auth()->id())->get();
        return view('paper_setter.questions.index', compact('questions'));
    }

    /** Show form to create a new question. */
    public function createQuestion()
    {
        return view('paper_setter.questions.create');
    }

    /** Store a newly created question. */
    public function storeQuestion(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|string',
            'marks' => 'required|numeric|min:1',
        ]);

        Question::create([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'marks' => $request->marks,
            'created_by' => auth()->id(),
            'sent_to_moderator' => false,
        ]);

        return redirect()->route('paper_setter.questions.index')
            ->with('success', 'Question added successfully.');
    }

    /** Delete a specific question by ID. */
    public function destroyQuestion($id)
    {
        $question = Question::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $question->delete();

        return redirect()->route('paper_setter.questions.index')
            ->with('success', 'Question deleted successfully.');
    }

    /** Delete a question using route model binding and authorization. */
    public function destroyQuestionModel(Question $question)
    {
        $this->authorize('delete', $question);

        $question->delete();

        return redirect()->route('paper_setter.questions.index')
            ->with('success', 'Question deleted successfully.');
    }

    /** Send selected questions to Moderator for review. */
    public function sendToModerator(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $questions = Question::whereIn('id', $request->question_ids)
            ->where('created_by', auth()->id())
            ->where('sent_to_moderator', false);

        $count = $questions->count();

        $questions->update(['sent_to_moderator' => true]);

        return redirect()->route('paper_setter.questions.index')
            ->with('success', "$count question(s) sent to moderator successfully.");
    }
}
