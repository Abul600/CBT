<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Moderator\ModeratorController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\PaperSetterController;
use App\Http\Controllers\StudentController;

// ✅ Public Landing Page
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('welcome');
});

// ✅ Authenticated User Dashboard (Redirect Based on Role)
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

    // ✅ Paper Setter Management
    Route::prefix('paper_setters')->name('paper_setters.')->group(function () {
        Route::get('/', [ModeratorController::class, 'paperSetters'])->name('index');
        Route::get('/create', [ModeratorController::class, 'createPaperSetter'])->name('create');
        Route::post('/', [ModeratorController::class, 'storePaperSetter'])->name('store');
        Route::delete('/{paper_setter}', [ModeratorController::class, 'destroyPaperSetter'])->name('destroy');
    });

    // ✅ Exam Management
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy'); 
    });

    // ✅ Question Management
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [ExamController::class, 'questionIndex'])->name('index');
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
});
