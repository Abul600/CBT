<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Moderator\{
    ModeratorController,
    ExamController,
    PaperSetterController,
    QuestionReviewController
};
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\PaperSetter\{
    QuestionController,
    PaperSetterController as PaperSetterMainController
};

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Redirect
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', fn () => auth()->user()->redirectToRoleDashboard())->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Moderator Management
    Route::prefix('moderators')->name('moderators.')->group(function () {
        Route::get('/', [AdminController::class, 'moderators'])->name('index');
        Route::get('/create', [AdminController::class, 'createModerator'])->name('create');
        Route::post('/', [AdminController::class, 'storeModerator'])->name('store');
        Route::get('/{moderator}/edit', [AdminController::class, 'editModerator'])->name('edit');
        Route::put('/{moderator}', [AdminController::class, 'updateModerator'])->name('update');
        Route::delete('/{moderator}', [AdminController::class, 'destroyModerator'])->name('destroy');
    });

    // User Role Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'indexUsers'])->name('index');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUserRole'])->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| Moderator Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:moderator'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/dashboard', [ModeratorController::class, 'dashboard'])->name('dashboard');

    // Paper Setter Management
    Route::resource('paper_setters', PaperSetterController::class)->except(['show']);
    Route::put('/paper_setters/{id}/toggle', [PaperSetterController::class, 'toggleStatus'])->name('paper_setters.toggleStatus');

    // Exam Management
    Route::resource('exams', ExamController::class)->middleware('can:viewAny,App\Models\Exam');

    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/{exam}/select-questions', [ExamController::class, 'selectQuestions'])
            ->name('select_questions')
            ->middleware('can:selectQuestions,exam');

        Route::get('/{exam}/questions', [ExamController::class, 'viewExamQuestions'])->name('questions');
        Route::get('/{exam}/questions/view', [ExamController::class, 'viewQuestions'])->name('questions.view');
        Route::get('/{exam}/questions/create', [ExamController::class, 'createQuestion'])->name('questions.create');
        Route::post('/{exam}/questions', [ExamController::class, 'storeQuestion'])->name('questions.store');
        Route::patch('/{exam}/questions/{question}/unassign', [ExamController::class, 'unassign'])
            ->name('unassign_question')
            ->middleware('can:modifyQuestions,exam');

        Route::post('/{exam}/assign-questions', [ExamController::class, 'assignQuestions'])
            ->name('assign-questions')
            ->middleware('can:modifyQuestions,exam');

        Route::post('/{exam}/unassign-questions', [ExamController::class, 'unassignQuestions'])
            ->name('unassign-questions')
            ->middleware('can:modifyQuestions,exam');

        // ✅ Release Exam Route
        Route::post('/{exam}/release', [ExamController::class, 'release'])->name('release');
    });

    Route::post('/assign-questions', [ExamController::class, 'assignQuestionsToExam'])->name('assign.questions');
    Route::delete('/questions/{question}', [ExamController::class, 'destroyQuestion'])->name('questions.destroy');

    // Review Questions
    Route::prefix('review')->name('review.')->group(function () {
        Route::get('/questions', [QuestionReviewController::class, 'index'])->name('questions.index');
        Route::post('/questions/{id}/approve', [QuestionReviewController::class, 'approve'])->name('questions.approve');
        Route::post('/questions/{id}/reject', [QuestionReviewController::class, 'reject'])->name('questions.reject');
    });

    Route::get('/questions/search', [ModeratorController::class, 'searchQuestions'])->name('search-questions');
});

/*
|--------------------------------------------------------------------------
| Paper Setter Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:paper_setter'])->prefix('paper_setter')->name('paper_setter.')->group(function () {
    Route::get('/dashboard', [PaperSetterMainController::class, 'dashboard'])->name('dashboard');

    Route::resource('questions', QuestionController::class)->except(['show']);
    Route::post('/questions/sendToModerator', [QuestionController::class, 'sendToModerator'])->name('questions.sendToModerator');

    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [PaperSetterMainController::class, 'examIndex'])->name('index');
        Route::get('/create', [PaperSetterMainController::class, 'createExam'])->name('create');
        Route::post('/', [PaperSetterMainController::class, 'storeExam'])->name('store');
        Route::delete('/{exam}', [PaperSetterMainController::class, 'destroyExam'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewExam'])->name('view')->middleware('can:view,exam');
        Route::get('/{exam}/start', [StudentController::class, 'startExam'])->name('start');
        Route::post('/{exam}/submit', [StudentController::class, 'submitExam'])->name('submit');
        Route::post('/{exam}/apply', [StudentController::class, 'apply'])->name('apply');
    });

    Route::get('/take-exam', [StudentController::class, 'takeExam'])->name('take.exam');
    Route::get('/view-results', [StudentController::class, 'viewResults'])->name('view.results');
    Route::get('/study-materials', [StudentController::class, 'studyMaterials'])->name('study.materials');
    Route::get('/search', [StudentController::class, 'search'])->name('search');

    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [StudentController::class, 'resultIndex'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewResult'])->name('view');
    });
});

/*
|--------------------------------------------------------------------------
| Moderator Activation Routes (Public)
|--------------------------------------------------------------------------
*/
Route::get('/moderators/activate/{id}', [ModeratorController::class, 'activate'])->name('moderator.activate');
Route::get('/moderators/deactivate/{id}', [ModeratorController::class, 'deactivate'])->name('moderator.deactivate');

/*
|--------------------------------------------------------------------------
| Additional Routes
|--------------------------------------------------------------------------
*/
//  Public route for starting mock exams
Route::get('/exams/mock/{exam}', [StudentController::class, 'startMockExam'])
    ->name('exams.mock.start')
    ->middleware(['auth', 'role:student']);
    Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/exams/{exam}/apply', [StudentController::class, 'apply'])->name('exam.apply');
    });
    Route::middleware(['auth', 'role:moderator'])->prefix('moderator')->name('moderator.')->group(function () {
        Route::prefix('exams/{exam}/questions')->name('exams.questions.')->group(function () {
            Route::post('{question}/approve', [ExamController::class, 'approveQuestion'])->name('approve');
        });
    });
    Route::post('/moderator/exams/{exam}/questions/{question}/reject', [ExamQuestionController::class, 'reject'])
    ->name('moderator.exams.questions.reject');
    Route::get('/moderator/questions/{question}', [QuestionController::class, 'show'])
    ->name('moderator.exams.questions.show');
    Route::get('/student/exams/start/{exam}', [StudentController::class, 'startExam'])->name('student.exams.start');
    Route::prefix('student/exams')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('student.exams.index');
        Route::get('/{exam}', [StudentController::class, 'view'])->name('student.exams.view');
        Route::post('/{exam}/start', [StudentController::class, 'start'])->name('student.exams.start');
    });
    Route::prefix('moderator')->name('moderator.')->group(function() {
        // other routes...
    
        Route::post('questions/{question}/unassign', [QuestionController::class, 'unassign'])->name('questions.unassign');
    });
    // Results routes
Route::prefix('results')->group(function () {
    // Student routes
    Route::get('/{result}', [ResultController::class, 'show'])
        ->name('student.results.show')
        ->middleware('auth');
    
    // Moderator routes
    Route::middleware(['auth', 'can:manage-results'])->group(function () {
        Route::get('/exam/{exam}', [ResultController::class, 'index'])
            ->name('moderator.results.index');
            
        Route::post('/exam/{exam}/calculate', [ResultController::class, 'calculateFinalResults'])
            ->name('results.calculate');
            
        Route::post('/exam/{exam}/release', [ResultController::class, 'releaseResults'])
            ->name('results.release');
    });
});
// Paper setter routes
Route::prefix('paper-setter')->group(function () {
    Route::get('/exams', [PaperSetterController::class, 'pendingExams'])
         ->name('paper-setter.exams.index');
         
    Route::get('/exams/{exam}/answers', [PaperSetterController::class, 'showExamAnswers'])
         ->name('paper-setter.exams.answers');
         
    Route::post('/exams/{exam}/bulk-grade', [PaperSetterController::class, 'bulkGrade'])
         ->name('paper-setter.bulk-grade');
});
Route::get('/student/exams/{exam}/result', [App\Http\Controllers\StudentController::class, 'viewResult'])
    ->name('student.viewResult')
    ->middleware(['auth', 'role:student']);
Route::get('/results/{result}', [ResultController::class, 'show'])->name('student.results.show')->middleware('auth');
// For paper setter exams
Route::prefix('paper_setter')->middleware(['auth', 'role:paper_setter'])->group(function () {
    Route::get('/exams', [PaperSetterController::class, 'examIndex'])->name('paper_setter.exams.index');
    Route::get('/exams/{exam}/grade', [PaperSetterController::class, 'gradeSubmissions'])->name('paper_setter.exams.grade');
    Route::post('/exams/{exam}/release', [PaperSetterController::class, 'releaseResults'])->name('paper_setter.exams.release');
});
// routes of paper setter for managing exams and grading
Route::prefix('paper_setter')->middleware(['auth', 'role:paper_setter'])->group(function () {
    Route::get('/exams', [PaperSetterController::class, 'examIndex'])->name('paper_setter.exams.index');
    Route::get('/exams/{exam}/grade', [PaperSetterController::class, 'gradeSubmissions'])->name('paper_setter.exams.grade');
    Route::post('/exams/{exam}/release', [PaperSetterController::class, 'releaseResults'])->name('paper_setter.exams.release');
    Route::put('/answers/{answer}', [PaperSetterController::class, 'updateMarks'])->name('paper_setter.answers.update');
});