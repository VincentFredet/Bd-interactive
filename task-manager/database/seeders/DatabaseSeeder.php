<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer des utilisateurs de test
        $user1 = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Créer des contextes de test
        $contexts = [
            \App\Models\Context::create(['name' => 'Scale Theme']),
            \App\Models\Context::create(['name' => 'Tap It']),
            \App\Models\Context::create(['name' => 'Vidéos Milo']),
            \App\Models\Context::create(['name' => 'Perso']),
        ];

        // Créer des tâches de test
        \App\Models\Task::create([
            'title' => 'Finaliser le design de la page d\'accueil',
            'description' => 'Terminer le mockup et valider avec l\'équipe',
            'status' => 'in_progress',
            'priority' => 'high',
            'context_id' => $contexts[0]->id,
            'user_id' => $user1->id,
        ]);

        \App\Models\Task::create([
            'title' => 'Corriger le bug du système de score',
            'description' => 'Le score ne se met pas à jour correctement après une partie',
            'status' => 'todo',
            'priority' => 'urgent',
            'context_id' => $contexts[1]->id,
            'user_id' => $user2->id,
        ]);

        \App\Models\Task::create([
            'title' => 'Monter la vidéo de présentation',
            'description' => 'Assembler les rushes et ajouter la musique',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $contexts[2]->id,
            'user_id' => $user1->id,
        ]);

        \App\Models\Task::create([
            'title' => 'Faire les courses',
            'description' => 'Acheter les ingrédients pour le dîner de demain',
            'status' => 'done',
            'priority' => 'low',
            'context_id' => $contexts[3]->id,
            'user_id' => $user2->id,
        ]);
    }
}
