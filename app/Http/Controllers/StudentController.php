<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\StudyMaterial;
use App\Models\District;
use App\Models\Result;
use App\Models\DescriptiveAnswer;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        $openExams = Exam::where('application_start', '<=', now())
            ->where('application_end', '>=', now())
            ->get();

        return view('student.dashboard', compact('openExams'));
    }

    public function index(Request $request)
    {
        $districts = District::all();
        $selectedDistrict = $request->get('district');

        $query = Exam::with('district')
            ->where('is_released', true)
            ->when($selectedDistrict, fn($q) => $q->where('district_id', $selectedDistrict))
            ->orderBy('created_at', 'desc');

        return view('student.exams.index', [
            'exams' => $query->paginate(10),
            'districts' => $districts,
            'selectedDistrict' => $selectedDistrict
        ]);
    }

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

    public function apply(Exam $exam)
    {
        auth()->user()->appliedExams()->syncWithoutDetaching([$exam->id]);
        return back()->with('success', 'Application submitted!');
    }

    public function viewResults()
    {
        $results = auth()->user()->results ?? collect();
        return view('student.results', compact('results'));
    }

    public function resultIndex()
    {
        $student = auth()->user();

        $results = $student->exams()
            ->with('questions', 'pivot')
            ->wherePivot('status', 'completed')
            ->get();

        return view('student.results', compact('results'));
    }

    public function studyMaterials()
    {
        $materials = StudyMaterial::all();
        return view('student.materials', compact('materials'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Question::where('question_text', 'LIKE', "%{$query}%")->get();

        return view('student.search-results', compact('results'));
    }

    public function view(Exam $exam)
    {
        return view('student.exam-view', compact('exam'));
    }

    public function startMockExam(Exam $exam)
    {
        if (!$exam->is_mock) {
            abort(403, 'This is not a mock test.');
        }

        return view('student.exam-view', compact('exam'));
    }

    public function startExam(Exam $exam)
    {
        if (!$exam->is_mock && !auth()->user()->appliedExams->contains($exam->id)) {
            return redirect()->route('student.exams.index')->with('error', 'You must apply to the exam first.');
        }

        $questions = $exam->questions()->inRandomOrder()->get();

        return view('student.exam-take', compact('exam', 'questions'));
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|string',
        ]);

        $mcqScore = 0;
        $totalMarks = 0;

        foreach ($validated['answers'] as $questionId => $studentAnswer) {
            $question = Question::findOrFail($questionId);
            $totalMarks += $question->marks;

            if (in_array($question->type, ['mcq1', 'mcq2'])) {
                $cleanAnswer = strtolower(trim($studentAnswer));
                $cleanCorrect = strtolower(trim($question->correct_option));

                \Log::info('Answer Check', [
                    'Question' => $question->id,
                    'Student Answer' => $studentAnswer,
                    'Correct Answer' => $question->correct_option,
                    'Match' => $cleanAnswer === $cleanCorrect
                ]);

                if ($cleanAnswer === $cleanCorrect) {
                    $mcqScore += $question->marks;
                }
            } else {
                DescriptiveAnswer::create([
                    'question_id' => $questionId,
                    'user_id' => auth()->id(),
                    'answer' => $studentAnswer,
                    'exam_id' => $exam->id
                ]);
            }
        }

        $percentage = $totalMarks > 0 ? ($mcqScore / $totalMarks) * 100 : 0;

        // Load actual question count if needed
        $exam->loadCount('questions');

        $result = Result::updateOrCreate(
            ['exam_id' => $exam->id, 'user_id' => auth()->id()],
            [
                'mcq_score'   => $mcqScore,
                'descriptive_score' => null, // If descriptive to be graded later
                'total'       => $totalMarks,
                'score'       => $mcqScore,
                'percentage'  => $percentage,
                'auto_graded' => $exam->auto_gradable,
                'status'      => $exam->auto_gradable ? 'graded' : 'pending_review',
            ]
        );

        return redirect()->route('student.results.show', $result)->with('success', 'Exam submitted successfully!');
    }

    public function viewResult(Exam $exam)
    {
        $result = auth()->user()->results()->where('exam_id', $exam->id)->first();
        return view('student.result-view', compact('result'));
    }
}
