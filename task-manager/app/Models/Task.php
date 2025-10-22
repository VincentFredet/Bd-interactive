<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
        'week_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function context(): BelongsTo
    {
        return $this->belongsTo(Context::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
