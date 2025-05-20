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
     * Show the student dashboard.
     */
    public function dashboard()
    {
        return view('student.dashboard');
    }

    /**
     * Show a list of available exams to take (with optional district filtering).
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
     * View all results for the authenticated student.
     */
    public function viewResults()
    {
        $results = auth()->user()->results ?? collect();
        return view('student.results', compact('results'));
    }

    /**
     * Display available study materials (no policy applied).
     */
    public function studyMaterials()
    {
        $materials = StudyMaterial::all();
        return view('student.materials', compact('materials'));
    }

    /**
     * Search questions based on a query string.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Question::where('content', 'LIKE', "%{$query}%")->get();

        return view('student.search-results', compact('results'));
    }

    /**
     * View a specific exam's detail (with access control).
     */
    public function viewExam(Exam $exam)
    {
        if (!Auth::user()->hasRole('student') || Auth::user()->district_id !== $exam->district_id) {
            abort(403);
        }

        return view('student.exams.view', compact('exam'));
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
     * Display a specific exam result.
     */
    public function viewResult(Exam $exam)
    {
        $result = auth()->user()->results()->where('exam_id', $exam->id)->first();
        return view('student.result-view', compact('result'));
    }
}
