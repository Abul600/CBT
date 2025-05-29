<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\DescriptiveAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ResultController extends Controller
{
    /**
     * Calculate final results (for scheduled exams)
     */
    public function calculateFinalResults(Exam $exam)
    {
        if (!Gate::allows('manage-results')) {
            abort(403);
        }

        // Ensure all descriptive answers are graded
        $ungradedAnswers = DescriptiveAnswer::where('exam_id', $exam->id)
            ->whereNull('marks')
            ->exists();

        if ($ungradedAnswers) {
            return redirect()->back()
                ->withErrors('Cannot finalize results - some descriptive answers are still ungraded');
        }

        // Load exam questions once
        $exam->loadMissing('questions');
        $mcqTotal = $exam->questions->whereIn('type', ['mcq', 'mcq1', 'mcq2'])->sum('marks');
        $descriptiveTotal = $exam->questions->where('type', 'descriptive')->sum('marks');
        $totalMarks = $mcqTotal + $descriptiveTotal;
        if ($totalMarks === 0) {
            $totalMarks = 1; // Prevent division by zero
        }

        // Process all results
        $results = Result::where('exam_id', $exam->id)->get();

        foreach ($results as $result) {
            $descriptiveScore = $result->descriptive_score ?? 0;
            $mcqScore = $result->mcq_score ?? 0;

            $totalScore = $mcqScore + $descriptiveScore;
            $percentage = ($totalScore / $totalMarks) * 100;

            $result->update([
                'descriptive_score' => $descriptiveScore,
                'score' => $totalScore,
                'percentage' => $percentage,
                'passed' => $percentage >= $exam->passing_marks,
                'status' => 'finalized',
                'total' => $totalMarks, // Save total for reuse
            ]);
        }

        return redirect()->back()->with('success', 'Final results calculated successfully');
    }

    /**
     * Release results to students
     */
    public function releaseResults(Exam $exam)
    {
        if (!Gate::allows('manage-results')) {
            abort(403);
        }

        $exam->update([
            'is_released' => true,
            'released_at' => now()
        ]);

        return redirect()->back()->with('success', 'Results released to students');
    }

    /**
     * Student view of individual result
     */
    public function show(Result $result)
    {
        // Restrict access to the owner
        if (auth()->id() !== $result->user_id) {
            abort(403);
        }

        // Ensure exam and questions are loaded
        $result->load('exam.questions');

        // Initialize values
        $mcqTotal = 0;
        $descriptiveTotal = 0;
        $totalMarks = 0;
        $examName = 'Unknown';

        if ($result->exam) {
            $examName = $result->exam->name;

            $mcqTotal = $result->exam->questions
                ->whereIn('type', ['mcq', 'mcq1', 'mcq2'])
                ->sum('marks');

            $descriptiveTotal = $result->exam->questions
                ->where('type', 'descriptive')
                ->sum('marks');

            $totalMarks = $mcqTotal + $descriptiveTotal;
        }

        return view('student.results.show', [
            'result' => $result,
            'mcqTotal' => $mcqTotal,
            'descriptiveTotal' => $descriptiveTotal,
            'totalMarks' => $totalMarks,
            'examName' => $examName
        ]);
    }

    /**
     * Moderator view of all results for a specific exam
     */
    public function index(Exam $exam)
    {
        if (!Gate::allows('manage-results')) {
            abort(403);
        }

        return view('moderator.results.index', [
            'exam' => $exam->load('results.user'),
            'results' => $exam->results()->with('user')->paginate(10)
        ]);
    }
}
