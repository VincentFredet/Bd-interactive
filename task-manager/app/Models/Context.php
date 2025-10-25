<?php

namespace App\Models;

use App\Traits\Colorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Context extends Model
{
    use Colorable;
    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * Available color options for contexts
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
     * Get tasks that have this context
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
