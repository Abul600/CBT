<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\StudyMaterial;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Show a list of available exams (with optional district filter).
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
     * View all results for the authenticated student.
     */
    public function viewResults()
    {
        $results = auth()->user()->results ?? collect();
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
        $results = Question::where('content', 'LIKE', "%{$query}%")->get();

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
     * Start a mock exam (mock exams only).
     */
    public function startMockExam(Exam $exam)
    {
        if (!$exam->is_mock) {
            abort(403, 'This is not a mock test.');
        }

        return view('student.exam-view', compact('exam'));
    }

    /**
     * Start a scheduled or mock exam (general handler).
     */
    public function startExam(Exam $exam)
    {
        // Optional: Check if the student has applied for this exam if it's not a mock
        if (!$exam->is_mock && !auth()->user()->appliedExams->contains($exam->id)) {
            return redirect()->route('student.exams.index')->with('error', 'You must apply to the exam first.');
        }

        return view('student.exam-take', compact('exam'));
    }

    /**
     * Submit exam answers (stub for now).
     */
    public function submitExam(Request $request, Exam $exam)
    {
        // TODO: Store answers, calculate results, etc.
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
