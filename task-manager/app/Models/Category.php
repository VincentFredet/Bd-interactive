<?php

namespace App\Models;

use App\Traits\Colorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use Colorable;
    protected $fillable = [
        'name',
        'color',
        'context_id',
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
     * Get tasks that have this category
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class);
    }

    /**
     * Get the context that owns this category
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(Context::class);
    }
}
