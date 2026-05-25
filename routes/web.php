<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/dashboard', function () {
    return view('dashboard/index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // routes/web.php
    Route::get('/projects/team', [TeamManagementController::class, 'index'])->name('teamManagement');
    Route::get('/projects/task', [TeamManagementController::class, 'index'])->name('taskManagement');
    Route::get('/projects/kanban', [TeamManagementController::class, 'index'])->name('kanbanBoard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
