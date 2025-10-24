<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'content',
    ];

    /**
     * Get the task that owns the comment.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user that wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get comments ordered by creation date.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get comments ordered by creation date (oldest first).
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Get a shortened version of the content.
     */
    public function getExcerptAttribute(): string
    {
        return \Str::limit($this->content, 100);
    }

    /**
     * Check if the comment was recently created (within last 5 minutes).
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at->diffInMinutes(now()) < 5;
    }
}
