<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContextController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskCommentController;
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

    // Categories routes
    Route::resource('categories', CategoryController::class);

    // Subtasks routes
    Route::post('/tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
    Route::patch('/subtasks/{subtask}', [SubtaskController::class, 'update'])->name('subtasks.update');
    Route::patch('/subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle'])->name('subtasks.toggle');
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');
    Route::post('/tasks/{task}/subtasks/reorder', [SubtaskController::class, 'reorder'])->name('subtasks.reorder');

    // Task comments routes
    Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'index'])->name('task-comments.index');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('task-comments.store');
    Route::patch('/comments/{comment}', [TaskCommentController::class, 'update'])->name('task-comments.update');
    Route::delete('/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('task-comments.destroy');

    // Notifications routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::patch('/notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-as-unread');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/delete-all-read', [NotificationController::class, 'deleteAllRead'])->name('notifications.delete-all-read');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
