<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\PaperSeaterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public dashboard (requires authentication & email verification)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Accessible only by users with 'admin' role
*/
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Moderator Management
    Route::prefix('moderators')->name('admin.moderators.')->group(function () {
        Route::get('/', [ModeratorController::class, 'index'])->name('index');
        Route::get('/create', [ModeratorController::class, 'create'])->name('create');
        Route::post('/', [ModeratorController::class, 'store'])->name('store');
        Route::get('/{moderator}/edit', [ModeratorController::class, 'edit'])->name('edit');
        Route::put('/{moderator}', [ModeratorController::class, 'update'])->name('update');
        Route::delete('/{moderator}', [ModeratorController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Moderator Routes
|--------------------------------------------------------------------------
| Accessible only by users with 'moderator' role
*/
Route::middleware(['auth:sanctum', 'role:moderator'])->prefix('moderator')->group(function () {
    Route::get('/dashboard', [ModeratorController::class, 'dashboard'])->name('moderator.dashboard');

    // Paper Seater Management
    Route::prefix('paper-seaters')->name('moderator.paper_seaters.')->group(function () {
        Route::get('/', [PaperSeaterController::class, 'index'])->name('index');
        Route::get('/create', [PaperSeaterController::class, 'create'])->name('create');
        Route::post('/', [PaperSeaterController::class, 'store'])->name('store');
        Route::delete('/{paper_seater}', [PaperSeaterController::class, 'destroy'])->name('destroy');
    });

    // Exam Management
    Route::prefix('exams')->name('moderator.exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
    });

    // Question Management
    Route::prefix('questions')->name('moderator.questions.')->group(function () {
        Route::get('/', [ExamController::class, 'questionIndex'])->name('index');
        Route::get('/create', [ExamController::class, 'createQuestion'])->name('create');
        Route::post('/', [ExamController::class, 'storeQuestion'])->name('store');
        Route::delete('/{question}', [ExamController::class, 'destroyQuestion'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Paper Seater Routes
|--------------------------------------------------------------------------
| Accessible only by users with 'paper_seater' role
*/
Route::middleware(['auth:sanctum', 'role:paper_seater'])->prefix('paper-seater')->group(function () {
    Route::get('/dashboard', [PaperSeaterController::class, 'dashboard'])->name('paper_seater.dashboard');
});       