<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\StudyMaterial;
use App\Models\District;
use App\Models\Result;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Show the student dashboard with open exams.
     */
    public function dashboard()
    {
        $openExams = Exam::where('application_start', '<=', now())
            ->where('application_end', '>=', now())
            ->get();

        return view('student.dashboard', compact('openExams'));
    }

    /**
     * Display released mock and applicable scheduled exams.
     */
    public function index(Request $request)
    {
        $selectedDistrict = $request->query('district');

        $exams = Exam::where('is_released', true)
            ->when($selectedDistrict, function ($query) use ($selectedDistrict) {
                return $query->where('district_id', $selectedDistrict);
            })
            ->with(['questions', 'district'])
            ->get()
            ->filter(function ($exam) {
                return $exam->type === 'mock' ||
                    ($exam->type === 'scheduled' && $exam->application_end > now());
            });

        $districts = District::all();

        return view('student.exams.index', compact('exams', 'districts', 'selectedDistrict'));
    }

    /**
     * Alternative route to display exams with optional district filter.
     */
    public function takeExam(Request $request)
    {
        $districts = District::all();
        $selectedDistrict = $request->input('district');

        $exams = Exam::when($selectedDistrict, function ($query) use ($selectedDistrict) {
                return $query->where('district_id', $selectedDistrict);
            })
            ->where('is_active', true)
            ->get();

        return view('student.exams', compact('districts', 'exams', 'selectedDistrict'));
    }

    /**
     * Apply for an exam.
     */
    public function apply(Exam $exam)
    {
        auth()->user()->appliedExams()->syncWithoutDetaching([$exam->id]);
        return back()->with('success', 'Application submitted!');
    }

    /**
     * Display all results for the authenticated student.
     */
    public function viewResults()
    {
        $results = auth()->user()->results ?? collect();
        return view('student.results', compact('results'));
    }

    /**
     * Results index page with completed exams.
     */
    public function resultIndex()
    {
        $student = auth()->user();

        $results = $student->exams()
            ->with('questions', 'pivot')
            ->wherePivot('status', 'completed')
            ->get();

        return view('student.results', compact('results'));
    }

    /**
     * Display all study materials.
     */
    public function studyMaterials()
    {
        $materials = StudyMaterial::all();
        return view('student.materials', compact('materials'));
    }

    /**
     * Search questions based on content.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Question::where('question_text', 'LIKE', "%{$query}%")->get();

        return view('student.search-results', compact('results'));
    }

    /**
     * View a specific exam.
     */
    public function viewExam(Exam $exam)
    {
        return view('student.exams.view', compact('exam'));
    }

    /**
     * Start a mock exam.
     */
    public function startMockExam(Exam $exam)
    {
        if (!$exam->is_mock) {
            abort(403, 'This is not a mock test.');
        }

        return view('student.exam-view', compact('exam'));
    }

    /**
     * Start a scheduled or mock exam.
     */
    public function startExam(Exam $exam)
    {
        if (!$exam->is_mock && !auth()->user()->appliedExams->contains($exam->id)) {
            return redirect()->route('student.exams.index')->with('error', 'You must apply to the exam first.');
        }

        $questions = $exam->questions()->inRandomOrder()->get();

        return view('student.exam-take', compact('exam', 'questions'));
    }

    /**
     * Submit exam answers and store result.
     */
    public function submitExam(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|string',
        ]);

        $answers = $validated['answers'];
        $score = 0;
        $total = 0;

        foreach ($answers as $questionId => $selectedOptionKey) {
            $question = Question::find($questionId);
            if ($question && $selectedOptionKey && $selectedOptionKey === $question->correct_option) {
                $score += $question->marks;
            }
            $total += $question ? $question->marks : 0;
        }

        Result::updateOrCreate(
            ['user_id' => auth()->id(), 'exam_id' => $exam->id],
            ['score' => $score, 'total' => $total]
        );

        return redirect()->route('student.exams.index')->with('success', 'Exam submitted successfully!');
    }

    /**
     * View a specific exam result.
     */
    public function viewResult(Exam $exam)
    {
        $result = auth()->user()->results()->where('exam_id', $exam->id)->first();
        return view('student.result-view', compact('result'));
    }
}
