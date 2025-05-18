<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\{Exam, Question, District};
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
        $districts = District::all();
        return view('moderator.exams.create', compact('districts'));
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

        $exam = Exam::create([
            ...$validated,
            'moderator_id' => Auth::id(),
            'district_id'  => Auth::user()->district_id,
            'status'       => 'draft',
        ]);

        return redirect()->route('moderator.exams.index')
            ->with('success', 'Exam created!');
    }

    public function destroy(Exam $exam)
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403, 'This action is unauthorized.');
        }

        $exam->delete();

        return redirect()->route('moderator.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function viewExamQuestions(Exam $exam)
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
        }

        $exam->load(['questions.paperSetter', 'pendingQuestions']);

        return view('moderator.exams.questions.index', compact('exam'));
    }

    public function viewQuestions(Request $request)
    {
        $moderator = Auth::user();
        $exams = Exam::where('moderator_id', $moderator->id)->get();

        $selectedExam = null;
        $availableQuestions = collect();

        if ($request->has('exam_id')) {
            $selectedExam = Exam::where('moderator_id', $moderator->id)
                ->find($request->exam_id);

            if ($selectedExam) {
                $availableQuestions = Question::whereNull('exam_id')
                    ->where('sent_to_moderator_id', $moderator->id)
                    ->get();
            }
        }

        return view('moderator.exams.view_questions', compact(
            'exams',
            'selectedExam',
            'availableQuestions'
        ));
    }

    public function viewPaperSetterQuestions(Exam $exam)
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
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
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
        }

        $assignedQuestions = $exam->questions()->with('paperSetter')->get();

        $availableQuestions = Question::with('paperSetter')
            ->where('status', 'approved')
            ->where('moderator_id', Auth::id())
            ->where(function ($query) use ($exam) {
                $query->whereNull('exam_id')
                      ->orWhere('exam_id', $exam->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('moderator.exams.manage_questions', compact('exam', 'assignedQuestions', 'availableQuestions'));
    }

    public function assignOrUnassign(Request $request, Exam $exam)
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
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
        $this->authorize('assignQuestions', $exam);

        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id,district_id,' . $exam->district_id,
        ]);

        Question::whereIn('id', $validated['question_ids'])
            ->where('district_id', $exam->district_id)
            ->update(['exam_id' => $exam->id]);

        return back()->with('success', 'Questions assigned successfully!');
    }

    public function unassign(Exam $exam, Question $question): RedirectResponse
    {
        if ($exam->moderator_id !== Auth::id() || $question->moderator_id !== Auth::id()) {
            abort(403);
        }

        $question->update(['exam_id' => null]);

        return redirect()
            ->route('moderator.exams.questions', ['exam' => $exam->id])
            ->with('success', 'Question unassigned successfully.');
    }

    public function createQuestion(Exam $exam)
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
        }

        return view('moderator.exams.questions.create', compact('exam'));
    }

    public function storeQuestion(Request $request, Exam $exam)
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content'   => 'required|string',
            'option_a'  => 'required|string',
            'option_b'  => 'required|string',
            'option_c'  => 'required|string',
            'option_d'  => 'required|string',
            'correct'   => 'required|in:A,B,C,D',
            'marks'     => 'required|numeric|min:0',
        ]);

        $exam->questions()->create(array_merge($validated, [
            'moderator_id' => Auth::id(),
            'status'       => 'approved',
        ]));

        return redirect()->route('moderator.exams.questions', ['exam' => $exam->id])
            ->with('success', 'Question added to exam.');
    }

    public function selectQuestions(Exam $exam)
    {
        $this->authorize('selectQuestions', $exam);

        // Get unassigned questions
        $unassignedQuestions = Question::where('district_id', $exam->district_id)
            ->whereNull('exam_id')
            ->get();

        // Get assigned questions for this exam
        $assignedQuestions = $exam->questions()->get();

        return view('moderator.exams.select_questions', [
            'exam' => $exam,
            'unassignedQuestions' => $unassignedQuestions,
            'assignedQuestions' => $assignedQuestions,
        ]);
    }
}
