<?php

<<<<<<< HEAD
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\PaperSetterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public home page
=======
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Moderator\ModeratorController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\PaperSeaterController;

// Public Landing Page
>>>>>>> ab83f84 (minor changes)
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('welcome'); // ✅ Prevents infinite redirect loops
});

<<<<<<< HEAD
// Protected Routes (Authenticated Users Only)
Route::middleware(['auth', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->redirectToRoleDashboard();
    })->name('dashboard');
});
=======
// Authenticated User Dashboard
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });
>>>>>>> ab83f84 (minor changes)

// 🔹 Admin Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

<<<<<<< HEAD
    // Moderator Management
    Route::resource('moderators', ModeratorController::class)->except(['show']);
=======
    // ✅ Moderator Management
    Route::prefix('moderators')->name('admin.moderators.')->group(function () {
        Route::get('/', [AdminController::class, 'indexModerators'])->name('index');
        Route::get('/create', [AdminController::class, 'createModerator'])->name('create');
        Route::post('/', [AdminController::class, 'storeModerator'])->name('store');
        Route::get('/{moderator}/edit', [AdminController::class, 'editModerator'])->name('edit');
        Route::put('/{moderator}', [AdminController::class, 'updateModerator'])->name('update');
        Route::delete('/{moderator}', [AdminController::class, 'destroyModerator'])->name('destroy');
    });
>>>>>>> ab83f84 (minor changes)
});

// 🔹 Moderator Routes
Route::middleware(['auth', 'role:Moderator'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/dashboard', [ModeratorController::class, 'dashboard'])->name('dashboard');

<<<<<<< HEAD
    // Paper Setter Management
    Route::resource('paper-setters', PaperSetterController::class)->only(['index', 'create', 'store', 'destroy']);

    // Exam Management
    Route::resource('exams', ExamController::class)->only(['index', 'create', 'store', 'destroy']);

    // Question Management
    Route::prefix('questions')->name('questions.')->group(function () {
=======
    // ✅ Paper Seater Management
    Route::prefix('paper-seaters')->name('moderator.paper_seaters.')->group(function () {
        Route::get('/', [ModeratorController::class, 'indexPaperSeaters'])->name('index');
        Route::get('/create', [ModeratorController::class, 'createPaperSeater'])->name('create');
        Route::post('/', [ModeratorController::class, 'storePaperSeater'])->name('store');
        Route::delete('/{paper_seater}', [ModeratorController::class, 'destroyPaperSeater'])->name('destroy');
    });

    // ✅ Exam Management
    Route::prefix('exams')->name('moderator.exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy'); 
    });

    // ✅ Question Management
    Route::prefix('questions')->name('moderator.questions.')->group(function () {
>>>>>>> ab83f84 (minor changes)
        Route::get('/', [ExamController::class, 'questionIndex'])->name('index');
        Route::get('/create', [ExamController::class, 'createQuestion'])->name('create');
        Route::post('/', [ExamController::class, 'storeQuestion'])->name('store');
        Route::delete('/{question}', [ExamController::class, 'destroyQuestion'])->name('destroy');
    });

    // ✅ Additional Moderator Routes
    Route::get('/paper-setters', [ModeratorController::class, 'paperSetters'])->name('moderator.paper-setters');
    Route::get('/questions/search', [ModeratorController::class, 'searchQuestions'])->name('moderator.search-questions');
});

// 🔹 Paper Setter Routes
Route::middleware(['auth', 'role:Paper Setter'])->prefix('paper-setter')->name('paper-setter.')->group(function () {
    Route::get('/dashboard', [PaperSetterController::class, 'dashboard'])->name('dashboard');
});

// 🔹 Student Routes
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
});
