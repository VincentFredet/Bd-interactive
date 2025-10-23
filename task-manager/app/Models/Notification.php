<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Notification types constants
     */
    const TYPE_TASK_CREATED = 'task_created';
    const TYPE_TASK_ASSIGNED = 'task_assigned';
    const TYPE_TASK_COMPLETED = 'task_completed';
    const TYPE_TASK_OVERDUE = 'task_overdue';
    const TYPE_COMMENT_ADDED = 'comment_added';
    const TYPE_SUBTASK_COMPLETED = 'subtask_completed';
    const TYPE_TASK_DUE_SOON = 'task_due_soon';

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'read' => true,
            'read_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update([
            'read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Scope to get only unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope to get only read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope to get notifications ordered by most recent.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if notification is recent (created in last 5 minutes).
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at->diffInMinutes(now()) < 5;
    }

    /**
     * Get icon for notification type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_TASK_CREATED => '📝',
            self::TYPE_TASK_ASSIGNED => '👤',
            self::TYPE_TASK_COMPLETED => '✅',
            self::TYPE_TASK_OVERDUE => '⚠️',
            self::TYPE_COMMENT_ADDED => '💬',
            self::TYPE_SUBTASK_COMPLETED => '☑️',
            self::TYPE_TASK_DUE_SOON => '⏰',
            default => '🔔',
        };
    }

    /**
     * Get color class for notification type.
     */
    public function getColorClassAttribute(): string
    {
        return match($this->type) {
            self::TYPE_TASK_CREATED => 'bg-blue-100 text-blue-800',
            self::TYPE_TASK_ASSIGNED => 'bg-purple-100 text-purple-800',
            self::TYPE_TASK_COMPLETED => 'bg-green-100 text-green-800',
            self::TYPE_TASK_OVERDUE => 'bg-red-100 text-red-800',
            self::TYPE_COMMENT_ADDED => 'bg-yellow-100 text-yellow-800',
            self::TYPE_SUBTASK_COMPLETED => 'bg-teal-100 text-teal-800',
            self::TYPE_TASK_DUE_SOON => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get human-readable time ago.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create a notification for a user.
     */
    public static function notify(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $actionUrl = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
        ]);
    }
}
