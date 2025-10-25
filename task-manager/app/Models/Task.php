<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'context_id',
        'user_id',
        'image',
        'week_date',
        'due_date',
        'completed_at',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_parent_id',
        'last_generated_at',
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
        'week_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'array',
        'last_generated_at' => 'datetime',
    ];

    public function context(): BelongsTo
    {
        return $this->belongsTo(Context::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the parent recurring task (if this is an instance)
     */
    public function recurrenceParent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'recurrence_parent_id');
    }

    /**
     * Get all instances generated from this recurring task
     */
    public function recurrenceInstances(): HasMany
    {
        return $this->hasMany(Task::class, 'recurrence_parent_id');
    }

    /**
     * Get all images for this task
     */
    public function images(): HasMany
    {
        return $this->hasMany(TaskImage::class)->orderBy('order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url('tasks/' . $this->image) : null;
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-yellow-100 text-yellow-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'todo' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'done' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the completion percentage of subtasks
     */
    public function getSubtasksCompletionPercentageAttribute(): int
    {
        $total = $this->subtasks->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->subtasks->where('completed', true)->count();
        return (int) round(($completed / $total) * 100);
    }

    /**
     * Check if all subtasks are completed
     */
    public function getAreAllSubtasksCompletedAttribute(): bool
    {
        $total = $this->subtasks->count();
        if ($total === 0) {
            return false;
        }

        return $this->subtasks->where('completed', true)->count() === $total;
    }

    /**
     * Get the number of completed subtasks
     */
    public function getCompletedSubtasksCountAttribute(): int
    {
        return $this->subtasks->where('completed', true)->count();
    }

    /**
     * Obtenir le début de la semaine pour une date donnée
     */
    public static function getWeekStart($date = null): Carbon
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        return $date->startOfWeek(Carbon::MONDAY);
    }

    /**
     * Obtenir la fin de la semaine pour une date donnée
     */
    public static function getWeekEnd($date = null): Carbon
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        return $date->endOfWeek(Carbon::SUNDAY);
    }

    /**
     * Scope pour filtrer par semaine
     */
    public function scopeForWeek($query, $weekStart)
    {
        return $query->where('week_date', $weekStart);
    }

    /**
     * Obtenir le libellé de la semaine
     */
    public function getWeekLabelAttribute(): string
    {
        if (!$this->week_date) {
            return 'Aucune semaine';
        }

        $weekStart = Carbon::parse($this->week_date);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        
        if ($weekStart->isCurrentWeek()) {
            return 'Cette semaine (' . $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m') . ')';
        }
        
        return 'Semaine du ' . $weekStart->format('d/m') . ' au ' . $weekEnd->format('d/m');
    }

    /**
     * Scope pour filtrer par date d'échéance
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('due_date', $date);
    }

    /**
     * Scope pour les tâches d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->where('due_date', Carbon::today());
    }

    /**
     * Scope pour les tâches en retard
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())
                    ->where('status', '!=', 'done');
    }

    /**
     * Vérifier si la tâche est en retard
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== 'done';
    }

    /**
     * Vérifier si la tâche est pour aujourd'hui
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->due_date && $this->due_date->isToday();
    }

    /**
     * Marquer la tâche comme terminée
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'done',
            'completed_at' => Carbon::now(),
        ]);
    }

    /**
     * Reporter la tâche à une date
     */
    public function postponeTo($date)
    {
        $this->update([
            'due_date' => Carbon::parse($date),
            'status' => $this->status === 'done' ? 'todo' : $this->status,
            'completed_at' => null,
        ]);
    }

    /**
     * Reporter au lendemain
     */
    public function postponeToTomorrow()
    {
        $this->postponeTo(Carbon::tomorrow());
    }

    /**
     * Obtenir le badge de classe pour la date d'échéance
     */
    public function getDueDateBadgeClassAttribute(): string
    {
        if (!$this->due_date) {
            return 'bg-gray-100 text-gray-800';
        }

        if ($this->is_overdue) {
            return 'bg-red-100 text-red-800';
        }

        if ($this->is_today) {
            return 'bg-blue-100 text-blue-800';
        }

        return 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if this task is a recurring task template
     */
    public function getIsRecurringTemplateAttribute(): bool
    {
        return $this->is_recurring && $this->recurrence_parent_id === null;
    }

    /**
     * Check if this task is a generated instance from a recurring task
     */
    public function getIsRecurringInstanceAttribute(): bool
    {
        return $this->recurrence_parent_id !== null;
    }

    /**
     * Get the next occurrence date based on the recurrence pattern
     */
    public function getNextOccurrenceDate(?Carbon $fromDate = null): ?Carbon
    {
        if (!$this->is_recurring || !$this->recurrence_pattern) {
            return null;
        }

        $fromDate = $fromDate ?? $this->last_generated_at ?? $this->due_date ?? Carbon::today();
        $pattern = $this->recurrence_pattern;

        $nextDate = match($pattern['type'] ?? 'daily') {
            'daily' => $fromDate->copy()->addDays($pattern['interval'] ?? 1),
            'weekly' => $fromDate->copy()->addWeeks($pattern['interval'] ?? 1),
            'monthly' => $fromDate->copy()->addMonths($pattern['interval'] ?? 1),
            'yearly' => $fromDate->copy()->addYears($pattern['interval'] ?? 1),
            default => null,
        };

        // Check if we have an end date and if we've passed it
        if (isset($pattern['end_date'])) {
            $endDate = Carbon::parse($pattern['end_date']);
            if ($nextDate && $nextDate->isAfter($endDate)) {
                return null;
            }
        }

        return $nextDate;
    }

    /**
     * Generate the next instance of this recurring task
     */
    public function generateNextInstance(): ?Task
    {
        if (!$this->is_recurring_template) {
            return null;
        }

        $nextDate = $this->getNextOccurrenceDate();
        if (!$nextDate) {
            return null;
        }

        // Create a new task instance
        $instance = $this->replicate([
            'is_recurring',
            'recurrence_pattern',
            'last_generated_at',
            'completed_at',
        ]);

        $instance->due_date = $nextDate;
        $instance->week_date = self::getWeekStart($nextDate);
        $instance->recurrence_parent_id = $this->id;
        $instance->status = 'todo';
        $instance->save();

        // Sync categories
        $instance->categories()->sync($this->categories->pluck('id'));

        // Update last generated timestamp on parent
        $this->update(['last_generated_at' => Carbon::now()]);

        return $instance;
    }

    /**
     * Scope to get only recurring templates
     */
    public function scopeRecurringTemplates($query)
    {
        return $query->where('is_recurring', true)
                    ->whereNull('recurrence_parent_id');
    }

    /**
     * Scope to get only recurring instances
     */
    public function scopeRecurringInstances($query)
    {
        return $query->whereNotNull('recurrence_parent_id');
    }

    /**
     * Get human-readable recurrence description
     */
    public function getRecurrenceDescriptionAttribute(): ?string
    {
        if (!$this->is_recurring || !$this->recurrence_pattern) {
            return null;
        }

        $pattern = $this->recurrence_pattern;
        $interval = $pattern['interval'] ?? 1;

        $description = match($pattern['type'] ?? 'daily') {
            'daily' => $interval === 1 ? 'Tous les jours' : "Tous les {$interval} jours",
            'weekly' => $interval === 1 ? 'Toutes les semaines' : "Toutes les {$interval} semaines",
            'monthly' => $interval === 1 ? 'Tous les mois' : "Tous les {$interval} mois",
            'yearly' => $interval === 1 ? 'Tous les ans' : "Tous les {$interval} ans",
            default => 'Récurrence personnalisée',
        };

        if (isset($pattern['end_date'])) {
            $endDate = Carbon::parse($pattern['end_date'])->format('d/m/Y');
            $description .= " (jusqu'au {$endDate})";
        }

        return $description;
    }

    /**
     * Définir automatiquement la semaine lors de la création si non spécifiée
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (!$task->week_date) {
                $task->week_date = self::getWeekStart();
            }
            // Si pas de date d'échéance, utiliser aujourd'hui
            if (!$task->due_date) {
                $task->due_date = Carbon::today();
            }
        });
    }
}
