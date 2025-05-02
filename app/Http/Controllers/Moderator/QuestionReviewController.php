<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\{Exam, Question};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class QuestionReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:moderator']);
    }

    public function index(Request $request): View
    {
        $moderatorId = auth()->id();
        
        $questions = Question::with(['paperSetter', 'exam'])
            ->where('moderator_id', $moderatorId)
            ->when($request->filled('exam_id'), function($query) use ($request) {
                $query->where('exam_id', $request->exam_id);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            }, function($query) {
                $query->whereIn('status', ['sent', 'pending']);
            })
            ->latest()
            ->get();

        $exams = Exam::where('moderator_id', $moderatorId)
            ->active()
            ->get();

        return view('moderator.questions.index', [
            'questions' => $questions,
            'exams' => $exams,
            'filters' => $request->only(['exam_id', 'status'])
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $question = Question::where('id', $id)
            ->where('moderator_id', auth()->id())
            ->where('status', 'sent')
            ->firstOrFail();

        $question->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        return back()->with('success', 'Question approved successfully.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $question = Question::where('id', $id)
            ->where('moderator_id', auth()->id())
            ->where('status', 'sent')
            ->firstOrFail();

        $question->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now()
        ]);

        return back()->with('success', 'Question rejected with feedback.');
    }

    public function assignToExam(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'exam_id' => [
                'required',
                Rule::exists('exams', 'id')
                    ->where('moderator_id', auth()->id())
            ]
        ]);

        $question->update([
            'exam_id' => $validated['exam_id'],
            'status' => 'assigned'
        ]);

        return back()->with('success', 'Question assigned to exam successfully.');
    }

    public function bulkAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => [
                'integer',
                Rule::exists('questions', 'id')
                    ->where('moderator_id', auth()->id())
                    ->whereNull('exam_id')
            ],
            'exam_id' => [
                'required',
                Rule::exists('exams', 'id')
                    ->where('moderator_id', auth()->id())
            ]
        ]);

        Question::whereIn('id', $validated['question_ids'])
            ->update(['exam_id' => $validated['exam_id']]);

        return back()->with('success', 'Selected questions assigned to exam.');
    }
}