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
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');

        // Select and assign questions
        Route::get('/{exam}/select-questions', [ExamController::class, 'selectQuestions'])->name('select_questions');
        Route::post('/{exam}/assign-questions', [ExamController::class, 'assignQuestions'])->name('assign_questions');

        // View exam questions
        Route::get('/{exam}/questions', [ExamController::class, 'viewQuestions'])->name('questions');
        Route::get('/view-questions', [ExamController::class, 'viewQuestions'])->name('view.questions');

        // Add question manually to exam
        Route::get('/{exam}/questions/create', [ExamController::class, 'createQuestion'])->name('questions.create');
        Route::post('/{exam}/questions', [ExamController::class, 'storeQuestion'])->name('questions.store');

        // Unassign question from exam (FIXED ROUTE)
        Route::patch('/{exam}/questions/{question}/unassign', [ExamController::class, 'unassign'])
             ->name('unassign_question');
    });

    // Question Management
    Route::delete('/questions/{question}', [ExamController::class, 'destroyQuestion'])->name('questions.destroy');
    Route::post('/assign-questions', [ExamController::class, 'assignQuestionsToExam'])->name('assign.questions');

    // Review Submitted Questions
    Route::prefix('review')->name('review.')->group(function () {
        Route::get('/questions', [QuestionReviewController::class, 'index'])->name('questions.index');
        Route::post('/questions/{id}/approve', [QuestionReviewController::class, 'approve'])->name('questions.approve');
        Route::post('/questions/{id}/reject', [QuestionReviewController::class, 'reject'])->name('questions.reject');
    });

    // Search Questions
    Route::get('/questions/search', [ModeratorController::class, 'searchQuestions'])->name('search-questions');
});

/*
|--------------------------------------------------------------------------
| Paper Setter Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:paper_setter'])->prefix('paper_setter')->name('paper_setter.')->group(function () {
    Route::get('/dashboard', [PaperSetterMainController::class, 'dashboard'])->name('dashboard');

    // Question Management
    Route::resource('questions', QuestionController::class)->except(['show']);
    Route::post('/questions/sendToModerator', [QuestionController::class, 'sendToModerator'])->name('questions.sendToModerator');

    // (Optional) Exam Management by Paper Setter
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [PaperSetterMainController::class, 'examIndex'])->name('index');
        Route::get('/create', [PaperSetterMainController::class, 'createExam'])->name('create');
        Route::post('/', [PaperSetterMainController::class, 'storeExam'])->name('store');
        Route::delete('/{exam}', [PaperSetterMainController::class, 'destroyExam'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Paper Seater Routes (Only Accessible by Paper Seaters)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:paper_seater'])->prefix('paper_seater')->name('paper_seater.')->group(function () {
    Route::get('/dashboard', [PaperSeaterController::class, 'dashboard'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [StudentController::class, 'examIndex'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewExam'])->name('view');
        Route::post('/{exam}/submit', [StudentController::class, 'submitExam'])->name('submit');
    });

    // Results
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [StudentController::class, 'resultIndex'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewResult'])->name('view');
    });
});

/*
|--------------------------------------------------------------------------
| Moderator Activation (Outside Role Guard)
|--------------------------------------------------------------------------
*/
Route::get('/moderators/activate/{id}', [ModeratorController::class, 'activate'])->name('moderator.activate');
Route::get('/moderators/deactivate/{id}', [ModeratorController::class, 'deactivate'])->name('moderator.deactivate');