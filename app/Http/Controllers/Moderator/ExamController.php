<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\{Exam, Question, District, StudyMaterial};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'type' => 'required|in:mock,scheduled',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'district_id' => 'nullable|exists:districts,id',
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'required_with:questions|string',
            'questions.*.type' => 'required_with:questions|in:mcq1,mcq2' . ($request->type === 'mock' ? '' : ',descriptive'),
            'questions.*.marks' => 'nullable|numeric|min:0',
        ];

        if ($request->type === 'scheduled') {
            $rules += [
                'application_start' => 'required|date|after:now',
                'application_end' => 'required|date|after:application_start',
                'exam_start' => 'required|date|after:application_end',
            ];
        }

        if (is_array($request->input('questions'))) {
            foreach ($request->input('questions') as $index => $q) {
                if (in_array($q['type'] ?? '', ['mcq1', 'mcq2'])) {
                    $rules["questions.$index.correct_option"] = 'required|in:A,B,C,D';
                }
            }
        }

        $validated = $request->validate($rules);

        if ($validated['type'] === 'mock' && isset($validated['questions'])) {
            foreach ($validated['questions'] as $question) {
                if (!in_array($question['type'], ['mcq1', 'mcq2'])) {
                    return back()->withErrors(['questions' => 'Mock exams can only contain MCQ questions']);
                }
            }
        }

        $districtId = $validated['district_id'] ?? Auth::user()->district_id;

        $exam = Exam::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'moderator_id' => Auth::id(),
            'district_id' => $districtId,
            'application_start' => $validated['application_start'] ?? null,
            'application_end' => $validated['application_end'] ?? null,
            'exam_start' => $validated['exam_start'] ?? null,
            'exam_end' => isset($validated['exam_start']) && $validated['type'] === 'scheduled'
                ? Carbon::parse($validated['exam_start'])->addMinutes((int)$validated['duration'])
                : null,
            'status' => 'draft',
            'is_active' => true,
            'auto_gradable' => $validated['type'] === 'mock',
        ]);

        if (!empty($validated['questions'])) {
            foreach ($validated['questions'] as $q) {
                $exam->questions()->create([
                    'question_text' => $q['question_text'],
                    'question_type' => $q['question_type'],
                    'correct_option' => $q['correct_option'] ?? null,
                    'marks' => $q['marks'] ?? 1,
                    'moderator_id' => Auth::id(),
                    'district_id' => $districtId,
                    'status' => Question::STATUS_APPROVED,
                ]);
            }
        }

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

        if ($exam->is_released) {
            return back()->withErrors(['exam' => 'Cannot modify questions after exam release']);
        }

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

        if ($exam->is_released) {
            return back()->withErrors(['exam' => 'Cannot modify questions after exam release']);
        }

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

        if ($exam->is_released) {
            return back()->withErrors(['exam' => 'Cannot modify questions after exam release']);
        }

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

        if ($exam->is_released) {
            return back()->withErrors(['exam' => 'Cannot unassign after exam release']);
        }

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

        if ($exam->is_released) {
            return back()->withErrors(['exam' => 'Cannot add questions after exam release']);
        }

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

    public function release(Exam $exam): RedirectResponse
    {
        $this->authorizeExam($exam);

        if ($exam->questions()->count() === 0) {
            return redirect()->back()->withErrors('Add questions before releasing');
        }

        $exam->update([
            'is_released' => true,
            'released_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Exam released to students');
    }

    public function convertToMock(Exam $exam): RedirectResponse
    {
        $this->authorizeExam($exam);

        if ($exam->type !== 'scheduled') {
            abort(400, 'Only scheduled exams can be converted');
        }

        DB::transaction(function () use ($exam) {
            // Fetch descriptive questions
            $descriptiveQuestions = $exam->questions()
                ->where('type', 'descriptive')
                ->get();

            if ($descriptiveQuestions->isNotEmpty()) {
                // Create study material for descriptive questions removed
                $studyMaterial = StudyMaterial::create([
                    'title' => "Removed Questions from {$exam->name}",
                    'original_exam_id' => $exam->id,
                    'district_id' => $exam->district_id,
                    'descriptive_answers' => '', // Initialize empty string
                ]);

                // Append questions' text to study material descriptive_answers and update each question
                foreach ($descriptiveQuestions as $question) {
                    $studyMaterial->descriptive_answers .= $question->question_text . "\n\n";
                    $question->update([
                        'study_material_id' => $studyMaterial->id,
                        // Detach question from exam, assuming pivot table has exam_id
                        // (detach handled after loop)
                    ]);
                }

                $studyMaterial->save();

                // Detach descriptive questions from exam after processing
                $exam->questions()->detach($descriptiveQuestions->pluck('id')->toArray());
            }

            // Update exam attributes to convert to mock
            $exam->update([
                'type' => 'mock',
                'application_start' => null,
                'application_end' => null,
                'exam_start' => null,
                'exam_end' => null,
                'status' => 'draft',
                'auto_gradable' => true,
                'is_active' => true,
            ]);
        });

        return redirect()->back()->with('success', 'Exam converted to mock successfully');
    }

    protected function authorizeExam(Exam $exam): void
    {
        if ($exam->moderator_id !== Auth::id()) {
            abort(403);
        }
    }
}
