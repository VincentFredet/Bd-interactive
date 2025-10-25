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
            // Add index on status column for filtering
            if (!Schema::hasIndex('tasks', 'tasks_status_index')) {
                $table->index('status', 'tasks_status_index');
            }

            // Add index on created_at for sorting
            if (!Schema::hasIndex('tasks', 'tasks_created_at_index')) {
                $table->index('created_at', 'tasks_created_at_index');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            // Add index on name for faster lookups and sorting
            if (!Schema::hasIndex('categories', 'categories_name_index')) {
                $table->index('name', 'categories_name_index');
            }
        });

        Schema::table('contexts', function (Blueprint $table) {
            // Add index on name for faster lookups and sorting
            if (!Schema::hasIndex('contexts', 'contexts_name_index')) {
                $table->index('name', 'contexts_name_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasIndex('tasks', 'tasks_status_index')) {
                $table->dropIndex('tasks_status_index');
            }

            if (Schema::hasIndex('tasks', 'tasks_created_at_index')) {
                $table->dropIndex('tasks_created_at_index');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasIndex('categories', 'categories_name_index')) {
                $table->dropIndex('categories_name_index');
            }
        });

        Schema::table('contexts', function (Blueprint $table) {
            if (Schema::hasIndex('contexts', 'contexts_name_index')) {
                $table->dropIndex('contexts_name_index');
            }
        });
    }
};
