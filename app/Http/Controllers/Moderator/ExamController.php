<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\{Exam, Question, District};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:mock,scheduled',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'district_id' => 'nullable|exists:districts,id',
        ];

        if ($request->type === 'scheduled') {
            $rules += [
                'application_start' => 'required|date|after:now',
                'application_end' => 'required|date|after:application_start',
                'exam_start' => 'required|date|after:application_end',
            ];
        } else {
            $rules += [
                'application_start' => 'nullable|date',
                'application_end' => 'nullable|date',
                'exam_start' => 'nullable|date',
            ];
        }

        $validated = $request->validate($rules);

        $districtId = $validated['district_id'] ?? Auth::user()->district_id;

        Exam::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'moderator_id' => Auth::id(),
            'district_id' => $districtId,
            'application_start' => $validated['application_start'] ?? null,
            'application_end' => $validated['application_end'] ?? null,
            'exam_start' => $validated['exam_start'] ?? null,
            'exam_end' => $validated['type'] === 'scheduled' && $validated['exam_start']
                ? Carbon::parse($validated['exam_start'])->addMinutes((int) $validated['duration'])
                : null,
            'status' => 'draft',
            'is_active' => true,
        ]);

        return redirect()->route('moderator.exams.index')->with('success', 'Exam created!');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $this->authorizeExam($exam);
        $exam->questions()->detach();
        $exam->delete();

        return redirect()->route('moderator.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function viewExamQuestions(Exam $exam)
    {
        $this->authorizeExam($exam);
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
            $selectedExam = Exam::where('moderator_id', $moderator->id)->find($request->exam_id);

            if ($selectedExam) {
                $availableQuestions = Question::whereDoesntHave('exams')
                    ->where('moderator_id', $moderator->id)
                    ->get();
            }
        }

        return view('moderator.exams.view_questions', compact('exams', 'selectedExam', 'availableQuestions'));
    }

    public function viewPaperSetterQuestions(Exam $exam)
    {
        $this->authorizeExam($exam);

        $questions = Question::with('paperSetter')
            ->where('status', 'sent')
            ->where('moderator_id', Auth::id())
            ->whereDoesntHave('exams')
            ->get();

        return view('moderator.exams.questions.assign', compact('exam', 'questions'));
    }

    public function manageQuestions(Exam $exam)
    {
        $this->authorizeExam($exam);

        $assignedQuestions = $exam->questions()->with('paperSetter')->get();

        $availableQuestions = Question::with('paperSetter')
            ->where('status', 'approved')
            ->where('moderator_id', Auth::id())
            ->whereDoesntHave('exams')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('moderator.exams.manage_questions', compact('exam', 'assignedQuestions', 'availableQuestions'));
    }

    public function assignOrUnassign(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeExam($exam);

        $assignIds = $request->input('assign_ids', []);
        $unassignIds = $request->input('unassign_ids', []);

        if (!empty($assignIds)) {
            $exam->questions()->syncWithoutDetaching($assignIds);
        }

        if (!empty($unassignIds)) {
            $exam->questions()->detach($unassignIds);
        }

        return redirect()
            ->route('moderator.exams.manage_questions', $exam->id)
            ->with('success', 'Question assignments updated.');
    }

    public function assignQuestions(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeExam($exam);

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $exam->questions()->syncWithoutDetaching($request->question_ids);

        return redirect()->back()->with('success', 'Questions assigned!');
    }

    public function unassignQuestions(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeExam($exam);

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $exam->questions()->detach($request->question_ids);

        return redirect()->back()->with('success', 'Questions unassigned!');
    }

    public function unassign(Exam $exam, Question $question): RedirectResponse
    {
        $this->authorizeExam($exam);

        if ($question->moderator_id !== Auth::id()) {
            abort(403);
        }

        $exam->questions()->detach($question->id);

        return redirect()
            ->route('moderator.exams.questions', ['exam' => $exam->id])
            ->with('success', 'Question unassigned successfully.');
    }

    public function createQuestion(Exam $exam)
    {
        $this->authorizeExam($exam);

        return view('moderator.exams.questions.create', compact('exam'));
    }

    public function storeQuestion(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeExam($exam);

        $validated = $request->validate([
            'question_text'  => 'required|string',
            'option_a'       => 'required|string',
            'option_b'       => 'required|string',
            'option_c'       => 'required|string',
            'option_d'       => 'required|string',
            'correct_option' => 'required|in:A,B,C,D',
            'marks'          => 'required|numeric|min:0',
        ]);

        $question = new Question(array_merge($validated, [
            'moderator_id' => Auth::id(),
            'district_id'  => $exam->district_id,
            'status'       => Question::STATUS_APPROVED,
        ]));

        $question->save();
        $exam->questions()->attach($question->id);

        return redirect()->route('moderator.exams.questions', ['exam' => $exam->id])
            ->with('success', 'Question added to exam.');
    }

    public function selectQuestions(Exam $exam)
    {
        $this->authorizeExam($exam);

        $unassignedQuestions = Question::where('district_id', $exam->district_id)
            ->whereDoesntHave('exams')
            ->get();

        $assignedQuestions = $exam->questions()->get();

        return view('moderator.exams.select_questions', compact(
            'exam',
            'unassignedQuestions',
            'assignedQuestions'
        ));
    }

    public function showQuestionAssignment(Exam $exam)
    {
        $assignedQuestions = $exam->questions;

        $unassignedQuestions = Question::whereDoesntHave('exams', function ($query) use ($exam) {
            $query->where('exam_id', $exam->id);
        })->get();

        return view('moderator.exams.select_questions', compact('exam', 'assignedQuestions', 'unassignedQuestions'));
    }

    protected function authorizeExam(Exam $exam): void
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
        }
    }
}
