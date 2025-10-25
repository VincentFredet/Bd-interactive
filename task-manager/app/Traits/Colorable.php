<?php

namespace App\Traits;

trait Colorable
{
    /**
     * Check if color is a custom hex color
     */
    protected function isCustomColor(): bool
    {
        return str_starts_with($this->color, '#');
    }

    /**
     * Get badge CSS classes based on color
     */
    public function getBadgeClassAttribute(): string
    {
        // Si c'est une couleur personnalisée, utiliser un style inline
        if ($this->isCustomColor()) {
            return ''; // Le style sera appliqué inline
        }

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
     * Get badge inline style for custom colors
     */
    public function getBadgeStyleAttribute(): string
    {
        if ($this->isCustomColor()) {
            $hex = $this->color;
            // Créer une version claire pour le fond
            return "background-color: {$hex}22; color: {$hex}; border: 1px solid {$hex}66;";
        }
        return '';
    }

    /**
     * Get hexadecimal color value for use in inline styles
     */
    public function getColorHexAttribute(): string
    {
        // Si c'est déjà un code hex, le retourner directement
        if ($this->isCustomColor()) {
            return $this->color;
        }

        $colorHex = [
            'gray' => '#6B7280',
            'blue' => '#3B82F6',
            'green' => '#10B981',
            'yellow' => '#F59E0B',
            'red' => '#EF4444',
            'purple' => '#8B5CF6',
            'pink' => '#EC4899',
            'indigo' => '#6366F1',
            'teal' => '#14B8A6',
            'orange' => '#F97316',
        ];

        return $colorHex[$this->color] ?? '#6B7280';
    }

    /**
     * Get button CSS classes for active state
     */
    public function getButtonActiveClassAttribute(): string
    {
        if ($this->isCustomColor()) {
            return ''; // Retourne vide, le style sera inline
        }

        $colorClasses = [
            'gray' => 'bg-gray-500 hover:bg-gray-700',
            'blue' => 'bg-blue-500 hover:bg-blue-700',
            'green' => 'bg-green-500 hover:bg-green-700',
            'yellow' => 'bg-yellow-500 hover:bg-yellow-700',
            'red' => 'bg-red-500 hover:bg-red-700',
            'purple' => 'bg-purple-500 hover:bg-purple-700',
            'pink' => 'bg-pink-500 hover:bg-pink-700',
            'indigo' => 'bg-indigo-500 hover:bg-indigo-700',
            'teal' => 'bg-teal-500 hover:bg-teal-700',
            'orange' => 'bg-orange-500 hover:bg-orange-700',
        ];

        return $colorClasses[$this->color] ?? 'bg-gray-500 hover:bg-gray-700';
    }

    /**
     * Get button inline style for active state (custom colors)
     */
    public function getButtonActiveStyleAttribute(): string
    {
        if ($this->isCustomColor()) {
            return "background-color: {$this->color}; color: white;";
        }
        return '';
    }

    /**
     * Get button CSS classes for inactive state
     */
    public function getButtonInactiveClassAttribute(): string
    {
        if ($this->isCustomColor()) {
            return 'bg-white border-2 border-gray-300 hover:bg-gray-100';
        }

        $colorClasses = [
            'gray' => 'bg-gray-200 text-gray-700 hover:bg-gray-300',
            'blue' => 'bg-blue-200 text-blue-700 hover:bg-blue-300',
            'green' => 'bg-green-200 text-green-700 hover:bg-green-300',
            'yellow' => 'bg-yellow-200 text-yellow-700 hover:bg-yellow-300',
            'red' => 'bg-red-200 text-red-700 hover:bg-red-300',
            'purple' => 'bg-purple-200 text-purple-700 hover:bg-purple-300',
            'pink' => 'bg-pink-200 text-pink-700 hover:bg-pink-300',
            'indigo' => 'bg-indigo-200 text-indigo-700 hover:bg-indigo-300',
            'teal' => 'bg-teal-200 text-teal-700 hover:bg-teal-300',
            'orange' => 'bg-orange-200 text-orange-700 hover:bg-orange-300',
        ];

        return $colorClasses[$this->color] ?? 'bg-gray-200 text-gray-700 hover:bg-gray-300';
    }

    /**
     * Get button inline style for inactive state (custom colors)
     */
    public function getButtonInactiveStyleAttribute(): string
    {
        if ($this->isCustomColor()) {
            return "color: {$this->color};";
        }
        return '';
    }

    /**
     * Get border CSS class based on color
     */
    public function getBorderClassAttribute(): string
    {
        if ($this->isCustomColor()) {
            return 'border-l-4';
        }

        $colorClasses = [
            'gray' => 'border-l-4 border-gray-500',
            'blue' => 'border-l-4 border-blue-500',
            'green' => 'border-l-4 border-green-500',
            'yellow' => 'border-l-4 border-yellow-500',
            'red' => 'border-l-4 border-red-500',
            'purple' => 'border-l-4 border-purple-500',
            'pink' => 'border-l-4 border-pink-500',
            'indigo' => 'border-l-4 border-indigo-500',
            'teal' => 'border-l-4 border-teal-500',
            'orange' => 'border-l-4 border-orange-500',
        ];

        return $colorClasses[$this->color] ?? 'border-l-4 border-gray-500';
    }

    /**
     * Get border inline style for custom colors
     */
    public function getBorderStyleAttribute(): string
    {
        if ($this->isCustomColor()) {
            return "border-left-color: {$this->color};";
        }
        return '';
    }
}
