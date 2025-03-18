<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\PaperSetterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public home page
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('welcome'); // ✅ Prevents infinite redirect loops
});

// Protected Routes (Authenticated Users Only)
Route::middleware(['auth', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->redirectToRoleDashboard();
    })->name('dashboard');
});

// 🔹 Admin Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Moderator Management
    Route::resource('moderators', ModeratorController::class)->except(['show']);
});

// 🔹 Moderator Routes
Route::middleware(['auth', 'role:Moderator'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/dashboard', [ModeratorController::class, 'dashboard'])->name('dashboard');

    // Paper Setter Management
    Route::resource('paper-setters', PaperSetterController::class)->only(['index', 'create', 'store', 'destroy']);

    // Exam Management
    Route::resource('exams', ExamController::class)->only(['index', 'create', 'store', 'destroy']);

    // Question Management
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [ExamController::class, 'questionIndex'])->name('index');
        Route::get('/create', [ExamController::class, 'createQuestion'])->name('create');
        Route::post('/', [ExamController::class, 'storeQuestion'])->name('store');
        Route::delete('/{question}', [ExamController::class, 'destroyQuestion'])->name('destroy');
    });
});

// 🔹 Paper Setter Routes
Route::middleware(['auth', 'role:Paper Setter'])->prefix('paper-setter')->name('paper-setter.')->group(function () {
    Route::get('/dashboard', [PaperSetterController::class, 'dashboard'])->name('dashboard');
});

// 🔹 Student Routes
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
});
