<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModeratorController;
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

// Admin routes (only accessible to authenticated admins)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Moderator Management Routes
    Route::get('/admin/moderators', [ModeratorController::class, 'index'])->name('admin.moderators.index');
    Route::get('/admin/moderators/create', [ModeratorController::class, 'create'])->name('admin.moderators.create');
    Route::post('/admin/moderators', [ModeratorController::class, 'store'])->name('admin.moderators.store');
    Route::get('/admin/moderators/{id}/edit', [ModeratorController::class, 'edit'])->name('admin.moderators.edit');
    Route::put('/admin/moderators/{id}', [ModeratorController::class, 'update'])->name('admin.moderators.update');
    Route::delete('/admin/moderators/{id}', [ModeratorController::class, 'destroy'])->name('admin.moderators.destroy');
});
