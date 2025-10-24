<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Assign rotating colors to existing contexts
        $contexts = DB::table('contexts')->whereNull('color')->orWhere('color', '')->get();

        $colors = ['blue', 'green', 'purple', 'orange', 'pink', 'indigo', 'teal', 'red', 'yellow'];
        $colorIndex = 0;

        foreach ($contexts as $context) {
            DB::table('contexts')
                ->where('id', $context->id)
                ->update(['color' => $colors[$colorIndex % count($colors)]]);

            $colorIndex++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse, as we're just setting default values
    }
};
