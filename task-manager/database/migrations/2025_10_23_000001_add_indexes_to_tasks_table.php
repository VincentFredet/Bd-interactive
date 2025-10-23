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
            // Index pour les requêtes de filtrage par semaine et statut
            $table->index(['week_date', 'status'], 'tasks_week_status_index');

            // Index pour les requêtes de filtrage par date d'échéance et statut
            $table->index(['due_date', 'status'], 'tasks_due_status_index');

            // Index pour le tri par priorité
            $table->index('priority', 'tasks_priority_index');

            // Index pour les requêtes de filtrage par contexte
            // Note: Foreign key index is already created, but we ensure it's optimized
            if (!Schema::hasIndex('tasks', 'tasks_context_id_index')) {
                $table->index('context_id', 'tasks_context_id_index');
            }

            // Index pour les requêtes de filtrage par utilisateur
            if (!Schema::hasIndex('tasks', 'tasks_user_id_index')) {
                $table->index('user_id', 'tasks_user_id_index');
            }

            // Index composite pour les tâches en retard (common query)
            $table->index(['due_date', 'status', 'priority'], 'tasks_overdue_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_week_status_index');
            $table->dropIndex('tasks_due_status_index');
            $table->dropIndex('tasks_priority_index');
            $table->dropIndex('tasks_overdue_index');

            // Only drop if they were created by this migration
            if (Schema::hasIndex('tasks', 'tasks_context_id_index')) {
                $table->dropIndex('tasks_context_id_index');
            }
            if (Schema::hasIndex('tasks', 'tasks_user_id_index')) {
                $table->dropIndex('tasks_user_id_index');
            }
        });
    }
};
