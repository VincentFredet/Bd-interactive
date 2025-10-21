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
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
        'week_date' => 'date',
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
     * Définir automatiquement la semaine lors de la création si non spécifiée
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            if (!$task->week_date) {
                $task->week_date = self::getWeekStart();
            }
        });
    }
}
