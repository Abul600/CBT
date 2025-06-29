<?php

namespace App\Http\Controllers\PaperSetter;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Display all questions created by the logged-in paper setter.
     */
    public function index()
    {
        $questions = Question::where('paper_setter_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

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
        $validated = $request->validate([
            'question_text'   => 'required|string|max:1000',
            'type'            => 'required|in:mcq1,mcq2,descriptive',
            'option_a'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'option_b'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'option_c'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'option_d'        => 'required_if:type,mcq1,mcq2|nullable|string',
            'correct_option'  => [
                'required_if:type,mcq1,mcq2',
                'nullable',
                Rule::in(['a', 'b', 'c', 'd']),
            ],
            'marks'           => 'exclude_if:type,mcq1|exclude_if:type,mcq2|required_if:type,descriptive|integer|min:1|max:100',
        ]);

        $user = Auth::user();

        if (!$user->district_id) {
            return redirect()
                ->route('paper_setter.questions.index')
                ->with('error', 'Your account is not assigned to a district. Contact the administrator.');
        }

        // Normalize correct_option to lowercase
        if (isset($validated['correct_option'])) {
            $validated['correct_option'] = strtolower($validated['correct_option']);
        }

        $marks = match ($validated['type']) {
            'mcq1' => 1,
            'mcq2' => 2,
            'descriptive' => $validated['marks'],
            default => 1,
        };

        Question::create([
            'district_id'       => $user->district_id,
            'exam_id'           => null,
            'paper_setter_id'   => $user->id,
            'question_text'     => $validated['question_text'],
            'option_a'          => $validated['option_a'] ?? null,
            'option_b'          => $validated['option_b'] ?? null,
            'option_c'          => $validated['option_c'] ?? null,
            'option_d'          => $validated['option_d'] ?? null,
            'correct_option'    => $validated['correct_option'] ?? null,
            'type'              => $validated['type'],
            'marks'             => $marks,
            'status'            => 'draft',
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

        // Validate input
        if (empty($questionIds)) {
            return redirect()->back()->with('error', 'No questions selected.');
        }

        $user = Auth::user();
        $moderatorId = $user->moderator_id;

        // Check if the paper setter has a linked moderator
        if (!$moderatorId) {
            return redirect()->back()
                ->with('error', 'You are not assigned to a moderator. Contact support.');
        }

        // Update questions
        $updatedCount = Question::whereIn('id', $questionIds)
            ->where('paper_setter_id', $user->id)
            ->where('status', 'draft')
            ->update([
                'status' => 'sent',
                'sent_to_moderator_id' => $moderatorId,
                'sent_at' => now(),
            ]);

        if ($updatedCount > 0) {
            return redirect()->back()
                ->with('success', "$updatedCount question(s) sent to moderator.");
        }

        return redirect()->back()
            ->with('error', 'No draft questions were sent. They may already be sent.');
    }

    /**
     * Delete selected questions created by the paper setter.
     */
    public function destroy(Request $request)
    {
        $questionIds = (array) $request->input('question_ids', []);

        if (empty($questionIds)) {
            return redirect()->back()->with('error', 'Please select at least one question to delete.');
        }

        $deletedCount = Question::whereIn('id', $questionIds)
            ->where('paper_setter_id', Auth::id())
            ->where('status', 'draft')
            ->delete();

        if ($deletedCount > 0) {
            return redirect()
                ->route('paper_setter.questions.index')
                ->with('success', "$deletedCount question(s) deleted successfully.");
        }

        return redirect()
            ->route('paper_setter.questions.index')
            ->with('error', 'No draft questions were deleted.');
    }

    /**
     * Show a single question (shared with moderator view).
     */
    public function show(Question $question)
    {
        return view('moderator.exams.questions.show', compact('question'));
    }
}
