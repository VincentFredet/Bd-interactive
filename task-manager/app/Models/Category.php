<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * Available color options for categories
     */
    public const COLORS = [
        'gray' => 'Gris',
        'blue' => 'Bleu',
        'green' => 'Vert',
        'yellow' => 'Jaune',
        'red' => 'Rouge',
        'purple' => 'Violet',
        'pink' => 'Rose',
        'indigo' => 'Indigo',
        'teal' => 'Turquoise',
        'orange' => 'Orange',
    ];

    /**
     * Get badge CSS classes based on color
     */
    public function getBadgeClassAttribute(): string
    {
        $colorClasses = [
            'gray' => 'bg-gray-100 text-gray-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'green' => 'bg-green-100 text-green-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'red' => 'bg-red-100 text-red-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'pink' => 'bg-pink-100 text-pink-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'teal' => 'bg-teal-100 text-teal-800',
            'orange' => 'bg-orange-100 text-orange-800',
        ];

        return $colorClasses[$this->color] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get tasks that have this category
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class);
    }
}
