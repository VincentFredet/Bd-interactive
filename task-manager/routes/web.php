<?php

use App\Http\Controllers\ContextController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect dashboard to tasks
    Route::get('/dashboard', function () {
        return redirect()->route('tasks.index');
    })->name('dashboard');
    
    // Tasks routes
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    
    // Daily task management
    Route::get('/daily', [TaskController::class, 'daily'])->name('tasks.daily');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/postpone', [TaskController::class, 'postpone'])->name('tasks.postpone');
    
    // Contexts routes
    Route::resource('contexts', ContextController::class);
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
