<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Moderator\ModeratorController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Moderator\PaperSetterController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Dashboard (Redirect Based on Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->redirectToRoleDashboard();
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Only Accessible by Admins)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ✅ Moderator Management
    Route::prefix('moderators')->name('moderators.')->group(function () {
        Route::get('/', [AdminController::class, 'moderators'])->name('index');
        Route::get('/create', [AdminController::class, 'createModerator'])->name('create');
        Route::post('/', [AdminController::class, 'storeModerator'])->name('store');
        Route::get('/{moderator}/edit', [AdminController::class, 'editModerator'])->name('edit');
        Route::put('/{moderator}', [AdminController::class, 'updateModerator'])->name('update');
        Route::delete('/{moderator}', [AdminController::class, 'destroyModerator'])->name('destroy');
    });

    // ✅ User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'indexUsers'])->name('index');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUserRole'])->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| Moderator Routes (Only Accessible by Moderators)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:moderator'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/dashboard', [ModeratorController::class, 'dashboard'])->name('dashboard');

    // ✅ Paper Setter Management (Resource Routes)
    Route::resource('paper_setters', PaperSetterController::class)->except(['show']);

    // ✅ Toggle Activation Status
    Route::put('/paper_setters/{id}/toggle', [PaperSetterController::class, 'toggleStatus'])
         ->name('paper_setters.toggleStatus');

    // ✅ Explicit Edit & Update Routes
    Route::get('/paper_setters/{paperSetter}/edit', [PaperSetterController::class, 'edit'])
         ->name('paper_setters.edit');
    Route::put('/paper_setters/{paperSetter}', [PaperSetterController::class, 'update'])
         ->name('paper_setters.update');

    // ✅ Exam Management
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
    });

    // ✅ Question Management
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [ExamController::class, 'indexQuestions'])->name('index');
        Route::get('/create', [ExamController::class, 'createQuestion'])->name('create');
        Route::post('/', [ExamController::class, 'storeQuestion'])->name('store');
        Route::delete('/{question}', [ExamController::class, 'destroyQuestion'])->name('destroy');
    });

    // ✅ Additional Moderator Routes
    Route::get('/questions/search', [ModeratorController::class, 'searchQuestions'])->name('search-questions');
});

/*
|--------------------------------------------------------------------------
| Paper Setter Routes (Only Accessible by Paper Setters)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:paper_setter'])->prefix('paper_setter')->name('paper_setter.')->group(function () {
    Route::get('/dashboard', [PaperSetterController::class, 'dashboard'])->name('dashboard');

    // ✅ Question Management for Paper Setters
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [PaperSetterController::class, 'questionIndex'])->name('index');
        Route::get('/create', [PaperSetterController::class, 'createQuestion'])->name('create');
        Route::post('/', [PaperSetterController::class, 'storeQuestion'])->name('store');
        Route::delete('/{question}', [PaperSetterController::class, 'destroyQuestion'])->name('destroy');
    });

    // ✅ Exam Management for Paper Setters
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [PaperSetterController::class, 'examIndex'])->name('index');
        Route::get('/create', [PaperSetterController::class, 'createExam'])->name('create');
        Route::post('/', [PaperSetterController::class, 'storeExam'])->name('store');
        Route::delete('/{exam}', [PaperSetterController::class, 'destroyExam'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Student Routes (Only Accessible by Students)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    // ✅ Student Exam Management
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [StudentController::class, 'examIndex'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewExam'])->name('view');
        Route::post('/{exam}/submit', [StudentController::class, 'submitExam'])->name('submit');
    });

    // ✅ Student Results
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [StudentController::class, 'resultIndex'])->name('index');
        Route::get('/{exam}', [StudentController::class, 'viewResult'])->name('view');
    });
});

/*
|--------------------------------------------------------------------------
| Moderator Activation & Deactivation Routes
|--------------------------------------------------------------------------
*/
Route::get('/moderators/activate/{id}', [ModeratorController::class, 'activate'])->name('moderator.activate');
Route::get('/moderators/deactivate/{id}', [ModeratorController::class, 'deactivate'])->name('moderator.deactivate');
