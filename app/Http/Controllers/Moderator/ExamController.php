<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\{Exam, Question};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:moderator']);
    }

    public function index()
    {
        $exams = Exam::where('moderator_id', Auth::id())
            ->withCount(['questions', 'pendingQuestions'])
            ->latest()
            ->get();

        return view('moderator.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('moderator.exams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration'    => 'required|integer|min:1',
            'start_time'  => 'required|date|after:now',
            'end_time'    => 'nullable|date|after:start_time',
        ]);

        Exam::create([
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'duration'     => $validated['duration'],
            'start_time'   => $validated['start_time'],
            'end_time'     => $validated['end_time'] ?? null,
            'moderator_id' => Auth::id(),
        ]);

        return redirect()->route('moderator.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    public function destroy(Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $exam->delete();
        return redirect()->route('moderator.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function viewExamQuestions(Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $exam->load(['questions.paperSetter', 'pendingQuestions']);
        return view('moderator.exams.questions.index', compact('exam'));
    }

    public function viewQuestions(Request $request, $examId = null)
    {
        $moderatorId = auth()->id();
        $exams = Exam::where('moderator_id', $moderatorId)->get();
        $selectedExamId = $examId;

        $questions = $selectedExamId
            ? Question::where('exam_id', $selectedExamId)
                      ->where('sent_to_moderator_id', $moderatorId)
                      ->get()
            : collect();

        return view('moderator.exams.view_questions', [
            'exams' => $exams,
            'questions' => $questions,
            'selectedExamId' => $selectedExamId,
        ]);
    }

    public function viewPaperSetterQuestions(Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $questions = Question::with('paperSetter')
            ->where('status', 'sent')
            ->where('moderator_id', Auth::id())
            ->whereNull('exam_id')
            ->get();

        return view('moderator.exams.questions.assign', compact('exam', 'questions'));
    }

    public function manageQuestions(Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $assignedQuestions = $exam->questions()->with('paperSetter')->get();
        $availableQuestions = Question::with('paperSetter')
            ->where('status', 'approved')
            ->where('moderator_id', Auth::id())
            ->whereNull('exam_id')
            ->get();

        return view('moderator.exams.manage_questions', compact('exam', 'assignedQuestions', 'availableQuestions'));
    }

    public function assignOrUnassign(Request $request, Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $assignIds = $request->input('assign_ids', []);
        if (!empty($assignIds)) {
            Question::whereIn('id', $assignIds)
                ->where('moderator_id', Auth::id())
                ->whereNull('exam_id')
                ->update(['exam_id' => $exam->id]);
        }

        $unassignIds = $request->input('unassign_ids', []);
        if (!empty($unassignIds)) {
            Question::whereIn('id', $unassignIds)
                ->where('exam_id', $exam->id)
                ->update(['exam_id' => null]);
        }

        return redirect()
            ->route('moderator.exams.manage_questions', $exam->id)
            ->with('success', 'Question assignments updated.');
    }

    public function assignQuestions(Request $request, Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);

        Question::whereIn('id', $request->question_ids)
            ->where('sent_to_moderator_id', Auth::id())
            ->whereNull('exam_id')
            ->update(['exam_id' => $exam->id]);

        return redirect()->route('moderator.exams.questions', ['exam' => $exam->id])
            ->with('success', 'Questions assigned to exam successfully.');
    }

    public function unassign(Exam $exam, Question $question): RedirectResponse
    {
        // Manual ownership checks
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        // Additional question ownership check
        if ($question->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $question->update(['exam_id' => null]);
        return redirect()
            ->route('moderator.exams.questions', ['exam' => $exam->id])
            ->with('success', 'Question unassigned successfully.');
    }

    public function createQuestion(Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        return view('moderator.exams.questions.create', compact('exam'));
    }

    public function storeQuestion(Request $request, Exam $exam)
    {
        // Manual ownership check
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $validated = $request->validate([
            'content'    => 'required|string',
            'option_a'   => 'required|string',
            'option_b'   => 'required|string',
            'option_c'   => 'required|string',
            'option_d'   => 'required|string',
            'correct'    => 'required|in:A,B,C,D',
            'marks'      => 'required|numeric|min:0',
        ]);

        $exam->questions()->create(array_merge($validated, [
            'moderator_id' => Auth::id(),
            'status'       => 'approved',
        ]));

        return redirect()->route('moderator.exams.questions', ['exam' => $exam->id])
            ->with('success', 'Question added to exam.');
    }
}
