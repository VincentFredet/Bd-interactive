<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Subtask extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'completed',
        'order',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'order' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the task that owns the subtask.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Mark the subtask as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark the subtask as incomplete.
     */
    public function markAsIncomplete(): void
    {
        $this->update([
            'completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Toggle the completion status.
     */
    public function toggleCompletion(): void
    {
        if ($this->completed) {
            $this->markAsIncomplete();
        } else {
            $this->markAsCompleted();
        }
    }

    /**
     * Scope to get only completed subtasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope to get only incomplete subtasks.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('completed', false);
    }

    /**
     * Scope to order by order column.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
