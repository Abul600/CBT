<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;

class ExamController extends Controller
{
    // Show all exams
    public function index()
    {
        $exams = Exam::all();
        return view('moderator.exams.index', compact('exams'));
    }

    // Show form to create a new exam
    public function create()
    {
        return view('moderator.exams.create');
    }

    // Store new exam
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        Exam::create([
            'title' => $request->title,
            'subject' => $request->subject,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('moderator.exams.index')->with('success', 'Exam created successfully.');
    }

    // Delete an exam
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('moderator.exams.index')->with('success', 'Exam deleted successfully.');
    }
}
