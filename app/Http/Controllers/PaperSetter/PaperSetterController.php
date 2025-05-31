<?php

namespace App\Http\Controllers\PaperSetter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\DescriptiveAnswer;
use App\Models\Answer;

class PaperSetterController extends Controller
{
    /**
     * Show the paper setter dashboard.
     */
    public function dashboard()
    {
        return view('paper_setter.dashboard');
    }

    /**
     * Show all exams with descriptive questions assigned to this moderator's paper setters.
     */
    public function examIndex()
    {
        // Get exams with descriptive questions that need grading
        $exams = Exam::whereHas('questions', function ($query) {
                $query->where('type', 'descriptive');
            })
            ->where('moderator_id', auth()->id()) // Exams under this moderator
            ->withCount([
                'submissions as ungraded_submissions_count' => function ($query) {
                    $query->where('is_graded', false);
                }
            ])
            ->get();

        return view('paper_setter.exams.index', compact('exams'));
    }

    /**
     * Show descriptive submissions needing grading.
     */
    public function gradeSubmissions(Exam $exam)
    {
        // Authorization check (optional)
        if ($exam->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $submissions = $exam->submissions()
            ->where('is_graded', false)
            ->with([
                'student',
                'answers' => function ($query) {
                    $query->whereHas('question', function ($q) {
                        $q->where('type', 'descriptive');
                    });
                }
            ])
            ->get();

        return view('paper_setter.exams.grade', compact('exam', 'submissions'));
    }

    /**
     * Update marks for a descriptive answer.
     */
    public function updateMarks(Request $request, DescriptiveAnswer $answer)
    {
        $request->validate([
            'marks' => 'required|numeric|min:0',
        ]);

        $answer->update(['marks' => $request->marks]);

        // If all descriptive answers in the submission are graded, mark submission as graded
        $ungradedCount = $answer->submission->descriptiveAnswers()
            ->whereNull('marks')
            ->count();

        if ($ungradedCount === 0) {
            $answer->submission->update(['is_graded' => true]);
        }

        return back()->with('success', 'Marks updated successfully!');
    }

    /**
     * Release the results for an exam (mark all as graded).
     */
    public function releaseResults(Exam $exam)
    {
        if ($exam->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $exam->submissions()->update(['is_graded' => true]);

        return back()->with('success', 'Results released successfully!');
    }
}
