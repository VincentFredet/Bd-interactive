<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;

class NotificationService
{
    /**
     * Notify when a new task is created.
     */
    public static function notifyTaskCreated(Task $task): void
    {
        // Notify all users except the creator
        $users = User::where('id', '!=', $task->user_id ?? auth()->id())->get();

        foreach ($users as $user) {
            Notification::notify(
                userId: $user->id,
                type: Notification::TYPE_TASK_CREATED,
                title: 'Nouvelle tâche créée',
                message: "Une nouvelle tâche \"{$task->title}\" a été créée",
                data: ['task_id' => $task->id],
                actionUrl: route('tasks.show', $task)
            );
        }
    }

    /**
     * Notify when a task is assigned to a user.
     */
    public static function notifyTaskAssigned(Task $task, User $assignedUser): void
    {
        Notification::notify(
            userId: $assignedUser->id,
            type: Notification::TYPE_TASK_ASSIGNED,
            title: 'Tâche assignée',
            message: "Vous avez été assigné à la tâche \"{$task->title}\"",
            data: ['task_id' => $task->id],
            actionUrl: route('tasks.show', $task)
        );
    }

    /**
     * Notify when a task is completed.
     */
    public static function notifyTaskCompleted(Task $task): void
    {
        // Notify all users except the one who completed it
        $users = User::where('id', '!=', auth()->id())->get();

        foreach ($users as $user) {
            Notification::notify(
                userId: $user->id,
                type: Notification::TYPE_TASK_COMPLETED,
                title: 'Tâche terminée',
                message: "La tâche \"{$task->title}\" a été marquée comme terminée",
                data: ['task_id' => $task->id],
                actionUrl: route('tasks.show', $task)
            );
        }
    }

    /**
     * Notify when a task is overdue.
     */
    public static function notifyTaskOverdue(Task $task): void
    {
        if ($task->user_id) {
            Notification::notify(
                userId: $task->user_id,
                type: Notification::TYPE_TASK_OVERDUE,
                title: 'Tâche en retard',
                message: "La tâche \"{$task->title}\" est en retard depuis {$task->due_date->diffForHumans()}",
                data: ['task_id' => $task->id],
                actionUrl: route('tasks.show', $task)
            );
        }
    }

    /**
     * Notify when a task is due soon (within 24 hours).
     */
    public static function notifyTaskDueSoon(Task $task): void
    {
        if ($task->user_id) {
            Notification::notify(
                userId: $task->user_id,
                type: Notification::TYPE_TASK_DUE_SOON,
                title: 'Échéance proche',
                message: "La tâche \"{$task->title}\" doit être terminée {$task->due_date->diffForHumans()}",
                data: ['task_id' => $task->id],
                actionUrl: route('tasks.show', $task)
            );
        }
    }

    /**
     * Notify when a comment is added to a task.
     */
    public static function notifyCommentAdded(TaskComment $comment): void
    {
        $task = $comment->task;

        // Notify task owner and all commenters except the one who just commented
        $userIds = collect([$task->user_id])
            ->merge($task->comments->pluck('user_id'))
            ->unique()
            ->filter(fn($id) => $id !== $comment->user_id)
            ->values();

        foreach ($userIds as $userId) {
            Notification::notify(
                userId: $userId,
                type: Notification::TYPE_COMMENT_ADDED,
                title: 'Nouveau commentaire',
                message: "{$comment->user->name} a commenté sur \"{$task->title}\"",
                data: [
                    'task_id' => $task->id,
                    'comment_id' => $comment->id,
                ],
                actionUrl: route('tasks.show', $task) . '#comment-' . $comment->id
            );
        }
    }

    /**
     * Notify when all subtasks are completed.
     */
    public static function notifyAllSubtasksCompleted(Task $task): void
    {
        if ($task->user_id) {
            Notification::notify(
                userId: $task->user_id,
                type: Notification::TYPE_SUBTASK_COMPLETED,
                title: 'Toutes les sous-tâches terminées',
                message: "Toutes les sous-tâches de \"{$task->title}\" sont terminées!",
                data: ['task_id' => $task->id],
                actionUrl: route('tasks.show', $task)
            );
        }
    }

    /**
     * Send daily digest notifications.
     */
    public static function sendDailyDigest(User $user): void
    {
        $todayTasks = $user->tasks()->today()->count();
        $overdueTasks = $user->tasks()->overdue()->count();
        $completedTasks = $user->tasks()
            ->whereDate('completed_at', today())
            ->count();

        if ($todayTasks > 0 || $overdueTasks > 0) {
            $message = "Résumé du jour: {$completedTasks} terminées";
            if ($todayTasks > 0) {
                $message .= ", {$todayTasks} pour aujourd'hui";
            }
            if ($overdueTasks > 0) {
                $message .= ", {$overdueTasks} en retard";
            }

            Notification::notify(
                userId: $user->id,
                type: 'daily_digest',
                title: 'Résumé quotidien',
                message: $message,
                data: [
                    'today_count' => $todayTasks,
                    'overdue_count' => $overdueTasks,
                    'completed_count' => $completedTasks,
                ],
                actionUrl: route('tasks.daily')
            );
        }
    }
}
