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
        $this->authorize('delete', $exam);
        $exam->delete();

        return redirect()->route('moderator.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function viewExamQuestions(Exam $exam)
    {
        $this->authorize('view', $exam);

        $exam->load(['questions.paperSetter', 'pendingQuestions']);

        return view('moderator.exams.questions.index', compact('exam'));
    }

    public function viewQuestions(Request $request)
    {
        $moderatorId = Auth::id();

        $questions = Question::where('sent_to_moderator_id', $moderatorId)
            ->whereNull('exam_id')
            ->get();

        $exams = Exam::where('moderator_id', $moderatorId)->get();

        $exam = null;

        if ($request->has('exam_id')) {
            $exam = Exam::with('questions.paperSetter')
                ->where('id', $request->exam_id)
                ->where('moderator_id', $moderatorId)
                ->first();
        }

        return view('moderator.exams.view_questions', compact('questions', 'exams', 'exam'));
    }

    public function viewPaperSetterQuestions(Exam $exam)
    {
        $this->authorize('update', $exam);

        $questions = Question::with('paperSetter')
            ->where('status', 'sent')
            ->where('moderator_id', Auth::id())
            ->whereNull('exam_id')
            ->get();

        return view('moderator.exams.questions.assign', compact('exam', 'questions'));
    }

    public function manageQuestions(Exam $exam)
    {
        $this->authorize('update', $exam);

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
        $this->authorize('update', $exam);

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

    public function assignQuestionsToExam(Request $request)
    {
        $request->validate([
            'exam_id'      => 'required|exists:exams,id',
            'question_ids' => 'required|array',
        ]);

        $examId = $request->exam_id;

        $exam = Exam::where('id', $examId)
            ->where('moderator_id', Auth::id())
            ->firstOrFail();

        Question::whereIn('id', $request->question_ids)
            ->where('sent_to_moderator_id', Auth::id())
            ->whereNull('exam_id')
            ->update(['exam_id' => $examId]);

        return redirect()->route('moderator.exams.questions', ['exam' => $examId])
            ->with('success', 'Questions assigned to exam successfully.');
    }

    public function unassign(Question $question): RedirectResponse
    {
        $this->authorize('update', $question);

        $examId = $question->exam_id;

        $question->update(['exam_id' => null]);

        return redirect()
            ->route('moderator.exams.questions', ['exam' => $examId])
            ->with('success', 'Question unassigned successfully.');
    }

    public function createQuestion(Exam $exam)
    {
        $this->authorize('update', $exam);
        return view('moderator.exams.questions.create', compact('exam'));
    }

    public function storeQuestion(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

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
