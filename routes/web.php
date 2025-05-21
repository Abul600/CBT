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

        Route::patch('/{exam}/questions/{question}/unassign', [ExamController::class, 'unassign'])->name('unassign_question');

        Route::post('/{exam}/assign-questions', [ExamController::class, 'assignQuestions'])
            ->name('assign-questions')
            ->middleware('can:assignQuestions,exam');

        Route::post('/{exam}/unassign-questions', [ExamController::class, 'unassignQuestions'])
            ->name('unassign-questions')
            ->middleware('can:assignQuestions,exam');
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
        Route::get('/', [StudentController::class, 'examIndex'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewExam'])->name('view')->middleware('can:view,exam');
        Route::post('/{exam}/submit', [StudentController::class, 'submitExam'])->name('submit');
    });

    // ✅ New Exam Apply Route
    Route::post('/exams/{exam}/apply', [StudentController::class, 'apply'])
        ->name('exams.apply')
        ->middleware(['auth', 'role:student']);

    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [StudentController::class, 'resultIndex'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewResult'])->name('view');
    });

    Route::get('/take-exam', [StudentController::class, 'takeExam'])->name('take.exam');
    Route::get('/view-results', [StudentController::class, 'viewResults'])->name('view.results');
    Route::get('/study-materials', [StudentController::class, 'studyMaterials'])->name('study.materials');
    Route::get('/search', [StudentController::class, 'search'])->name('search');
});

/*
|--------------------------------------------------------------------------
| Moderator Activation Routes
|--------------------------------------------------------------------------
*/
Route::get('/moderators/activate/{id}', [ModeratorController::class, 'activate'])->name('moderator.activate');
Route::get('/moderators/deactivate/{id}', [ModeratorController::class, 'deactivate'])->name('moderator.deactivate');
