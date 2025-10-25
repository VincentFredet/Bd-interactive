<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Indicate if this task is a recurring task template
            $table->boolean('is_recurring')->default(false)->after('status');

            // JSON field to store recurrence pattern
            // Format: {"type": "daily|weekly|monthly", "interval": 1, "days": [1,3,5], "end_date": "2024-12-31"}
            $table->json('recurrence_pattern')->nullable()->after('is_recurring');

            // Reference to the parent recurring task (if this is a generated instance)
            $table->foreignId('recurrence_parent_id')->nullable()->after('recurrence_pattern')
                  ->constrained('tasks')->onDelete('cascade');

            // Last date when a new instance was generated from this recurring task
            $table->timestamp('last_generated_at')->nullable()->after('recurrence_parent_id');

            // Add index for recurring tasks
            $table->index('is_recurring');
            $table->index('recurrence_parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['recurrence_parent_id']);
            $table->dropIndex(['is_recurring']);
            $table->dropIndex(['recurrence_parent_id']);
            $table->dropColumn([
                'is_recurring',
                'recurrence_pattern',
                'recurrence_parent_id',
                'last_generated_at'
            ]);
        });
    }
};
