<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Get hex color values for all available colors
     */
    public static function getColorHexMap(): array
    {
        return [
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
    }

    /**
     * Get a specific hex color value
     */
    public static function getColorHex(string $color): string
    {
        $hexMap = self::getColorHexMap();
        return $hexMap[$color] ?? '#6B7280';
    }
}
