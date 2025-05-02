<?php

namespace App\Http\Controllers\PaperSetter;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Display all questions created by the logged-in paper setter.
     */
    public function index()
    {
        $questions = Question::where('paper_setter_id', Auth::id())->get();
        return view('paper_setter.questions.index', compact('questions'));
    }

    /**
     * Show form to create a new question.
     */
    public function create()
    {
        return view('paper_setter.questions.create');
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_text'   => 'required|string|max:1000',
            'type'            => 'required|in:mcq1,mcq2,descriptive',
            'option_a'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'option_b'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'option_c'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'option_d'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'correct_option'  => 'required_if:type,mcq1,mcq2|nullable|in:A,B,C,D',
            'marks'           => 'exclude_if:type,mcq1|exclude_if:type,mcq2|required_if:type,descriptive|integer|min:1|max:100',
        ]);

        $marks = match ($request->type) {
            'mcq1' => 1,
            'mcq2' => 2,
            'descriptive' => $request->marks,
            default => 1,
        };

        Question::create([
            'exam_id'         => null,
            'paper_setter_id' => Auth::id(),
            'question_text'   => $request->question_text,
            'option_a'        => $request->option_a,
            'option_b'        => $request->option_b,
            'option_c'        => $request->option_c,
            'option_d'        => $request->option_d,
            'correct_option'  => $request->correct_option,
            'type'            => $request->type,
            'marks'           => $marks,
            'status'          => 'draft',
        ]);

        return redirect()
            ->route('paper_setter.questions.index')
            ->with('success', 'Question created successfully.');
    }

    /**
     * Send selected draft questions to the associated moderator.
     */
    public function sendToModerator(Request $request)
    {
        $questionIds = (array) $request->input('question_ids', []);
        $paperSetter = Auth::user();
        $moderatorId = $paperSetter->moderator_id;

        if (!$moderatorId) {
            return redirect()
                ->route('paper_setter.questions.index')
                ->with('error', 'No associated moderator found for this paper setter.');
        }

        $updatedCount = Question::whereIn('id', $questionIds)
            ->where('paper_setter_id', $paperSetter->id)
            ->where('status', 'draft')
            ->update([
                'status' => 'sent',
                'sent_to_moderator_id' => $moderatorId,
                'moderator_id' => $moderatorId, // ensures correct filtering later
            ]);

        if ($updatedCount > 0) {
            return redirect()
                ->route('paper_setter.questions.index')
                ->with('success', 'Questions sent to moderator.');
        }

        return redirect()
            ->route('paper_setter.questions.index')
            ->with('error', 'No draft questions were submitted.');
    }

    /**
     * Delete selected questions created by the paper setter.
     */
    public function destroy(Request $request)
    {
        $questionIds = (array) $request->input('question_ids', []);

        $deletedCount = Question::whereIn('id', $questionIds)
            ->where('paper_setter_id', Auth::id())
            ->where('status', 'draft') // Optional: Only allow deleting drafts
            ->delete();

        if ($deletedCount > 0) {
            return redirect()
                ->route('paper_setter.questions.index')
                ->with('success', 'Selected question(s) deleted.');
        }

        return redirect()
            ->route('paper_setter.questions.index')
            ->with('error', 'No questions were deleted.');
    }
}
