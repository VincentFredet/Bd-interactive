<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view tasks
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // All authenticated users can view any task
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create tasks
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // For now, all users can update any task
        // In the future, you could restrict this to:
        // - Only the assigned user: return $task->user_id === $user->id;
        // - Task owner or assigned user: return $task->created_by === $user->id || $task->user_id === $user->id;
        // - Admin users only: return $user->is_admin;

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // For now, all users can delete any task
        // In the future, you could restrict this to:
        // - Only the task creator
        // - Only admin users

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        // If you implement soft deletes, you can control restoration here
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        // If you implement soft deletes, you can control permanent deletion here
        return true;
    }

    /**
     * Determine whether the user can complete the task.
     */
    public function complete(User $user, Task $task): bool
    {
        // Anyone can complete a task
        return true;
    }

    /**
     * Determine whether the user can postpone the task.
     */
    public function postpone(User $user, Task $task): bool
    {
        // Anyone can postpone a task
        return true;
    }

    /**
     * Determine whether the user can update the task status.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        // Anyone can update task status
        return true;
    }
}
