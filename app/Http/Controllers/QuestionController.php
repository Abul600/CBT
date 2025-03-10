<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::all();
        return view('moderator.questions.index', compact('questions'));
    }

    public function create()
    {
        return view('moderator.questions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'marks' => 'required|integer',
        ]);

        Question::create([
            'question_text' => $request->question_text,
            'marks' => $request->marks,
        ]);

        return redirect()->route('moderator.questions.index')->with('success', 'Question added successfully');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('moderator.questions.index')->with('success', 'Question deleted successfully');
    }
}
