<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Question;
use App\Models\Exam;
use App\Models\DescriptiveAnswer;
use App\Models\Result;

class PaperSetterController extends Controller
{
    /** ========== Paper Setter Management ========== */

    public function index()
    {
        if (!Gate::allows('manage-paper-setters')) {
            abort(403);
        }

        $paperSetters = User::role('paper_setter')
            ->where('moderator_id', auth()->id())
            ->get();

        return view('moderator.paper_setters.index', compact('paperSetters'));
    }

    public function create()
    {
        $activeCount = User::role('paper_setter')
            ->where('moderator_id', auth()->id())
            ->where('is_active', 1)
            ->count();

        if ($activeCount >= 3) {
            return redirect()->route('moderator.paper_setters.index')
                ->with('error', 'You can only have 3 active paper setters at a time. Deactivate one to add a new.');
        }

        return view('moderator.paper_setters.create');
    }

    public function store(Request $request)
    {
        if (!Gate::allows('manage-paper-setters')) {
            abort(403);
        }

        $activeCount = User::role('paper_setter')
            ->where('moderator_id', auth()->id())
            ->where('is_active', 1)
            ->count();

        if ($activeCount >= 3) {
            return redirect()->route('moderator.paper_setters.index')
                ->with('error', 'Cannot add more than 3 active paper setters. Please deactivate one first.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $moderator = Auth::user();

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'password'     => Hash::make($request->password),
            'is_active'    => true,
            'is_moderator' => false,
            'moderator_id' => $moderator->id,
            'district_id'  => $moderator->district_id,
        ]);

        $user->assignRole('paper_setter');

        return redirect()->route('moderator.paper_setters.index')
            ->with('success', 'Paper Setter created successfully.');
    }

    public function edit(User $paperSetter)
    {
        if ($paperSetter->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('moderator.paper_setters.edit', compact('paperSetter'));
    }

    public function update(Request $request, User $paperSetter)
    {
        if ($paperSetter->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $paperSetter->id,
        ]);

        $paperSetter->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('moderator.paper_setters.index')
            ->with('success', 'Paper Setter updated successfully.');
    }

    public function destroyPaperSetter(User $paperSetter)
    {
        if ($paperSetter->moderator_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $paperSetter->delete();

        return redirect()->route('moderator.paper_setters.index')
            ->with('success', 'Paper Setter deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $user = User::role('paper_setter')
            ->where('id', $id)
            ->where('moderator_id', auth()->id())
            ->firstOrFail();

        if (!$user->is_active) {
            $activeCount = User::role('paper_setter')
                ->where('moderator_id', auth()->id())
                ->where('is_active', 1)
                ->count();

            if ($activeCount >= 3) {
                return redirect()->back()->with('error', 'Cannot activate more than 3 paper setters.');
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully!');
    }

    /** ========== Paper Setter Dashboard ========== */

    public function dashboard()
    {
        return view('paper_setter.dashboard');
    }

    public function exams()
    {
        $exams = Exam::where('paper_setter_id', Auth::id())->get();
        return view('paper_setter.exams', compact('exams'));
    }

    /** ========== Question Management ========== */

    public function questionIndex()
    {
        $questions = Question::where('created_by', auth()->id())->get();
        return view('paper_setter.questions.index', compact('questions'));
    }

    public function createQuestion()
    {
        return view('paper_setter.questions.create');
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|string',
            'marks'         => 'required|numeric|min:1',
        ]);

        Question::create([
            'question_text'     => $request->question_text,
            'type'              => $request->type,
            'marks'             => $request->marks,
            'created_by'        => auth()->id(),
            'sent_to_moderator' => false,
        ]);

        return redirect()->route('paper_setter.questions.index')
            ->with('success', 'Question added successfully.');
    }

    public function destroyQuestion($id)
    {
        $question = Question::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $question->delete();

        return redirect()->route('paper_setter.questions.index')
            ->with('success', 'Question deleted successfully.');
    }

    public function sendToModerator(Request $request)
    {
        $request->validate([
            'question_ids'   => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $questions = Question::whereIn('id', $request->question_ids)
            ->where('created_by', auth()->id())
            ->where('sent_to_moderator', false);

        $count = $questions->count();
        $questions->update(['sent_to_moderator' => true]);

        return redirect()->route('paper_setter.questions.index')
            ->with('success', "$count question(s) sent to moderator successfully.");
    }

    /** ========== Descriptive Answer Grading ========== */

    public function pendingExams()
    {
        $exams = Exam::whereHas('questions', function ($q) {
                $q->where('type', 'descriptive');
            })
            ->whereHas('descriptiveAnswers', function ($q) {
                $q->whereNull('marks');
            })
            ->withCount(['descriptiveAnswers as ungraded_count' => function ($q) {
                $q->whereNull('marks');
            }])
            ->paginate(10);

        return view('paper_setter.exams.index', compact('exams'));
    }

    public function showExamAnswers(Exam $exam)
    {
        $answers = DescriptiveAnswer::with(['question', 'user'])
            ->where('exam_id', $exam->id)
            ->whereNull('marks')
            ->paginate(10);

        return view('paper_setter.exams.answers', compact('exam', 'answers'));
    }

    public function gradeAnswer(Request $request, DescriptiveAnswer $answer)
    {
        $request->validate([
            'marks' => 'required|integer|min:0|max:' . $answer->question->marks,
        ]);

        $answer->update([
            'marks' => $request->marks,
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        $result = Result::firstOrNew([
            'exam_id' => $answer->exam_id,
            'user_id' => $answer->user_id,
        ]);

        $result->descriptive_score += $request->marks;
        $result->save();

        return back()->with('success', 'Marks updated successfully');
    }

    public function bulkGrade(Request $request, Exam $exam)
    {
        $request->validate([
            'marks.*' => 'required|integer|min:0',
        ]);

        foreach ($request->marks as $answerId => $marks) {
            $answer = DescriptiveAnswer::findOrFail($answerId);
            $questionMarks = $answer->question->marks;

            if ($marks > $questionMarks) {
                continue;
            }

            $answer->update([
                'marks' => $marks,
                'graded_by' => auth()->id(),
                'graded_at' => now(),
            ]);

            Result::updateOrCreate(
                ['exam_id' => $exam->id, 'user_id' => $answer->user_id],
                ['descriptive_score' => DB::raw("descriptive_score + $marks")]
            );
        }

        return redirect()->route('paper-setter.exams.answers', $exam)
            ->with('success', 'Bulk grading completed');
    }

    public function releaseResults(Exam $exam)
    {
        $exam->update(['results_released' => true]);
        return back()->with('success', 'Results released successfully!');
    }

    /** ========== Additional Exam Index ========== */

    public function examIndex()
    {
        $exams = Exam::whereHas('questions', function ($query) {
                $query->where('type', 'descriptive');
            })
            ->where('moderator_id', auth()->id())
            ->withCount('submissions')
            ->get();

        return view('paper_setter.exams.index', compact('exams'));
    }
}
