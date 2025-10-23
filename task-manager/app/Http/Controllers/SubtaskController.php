<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    /**
     * Store a newly created subtask.
     */
    public function store(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
            ], [
                'title.required' => 'Le titre de la sous-tâche est obligatoire',
                'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            ]);

            // Get the next order number
            $lastOrder = $task->subtasks()->max('order') ?? -1;

            $subtask = $task->subtasks()->create([
                'title' => $validated['title'],
                'order' => $lastOrder + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sous-tâche créée avec succès',
                'subtask' => $subtask,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating subtask', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la sous-tâche',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Update the specified subtask.
     */
    public function update(Request $request, Subtask $subtask)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'completed' => 'sometimes|boolean',
                'order' => 'sometimes|integer|min:0',
            ]);

            $subtask->update($validated);

            // If marking as completed/incomplete, update the timestamp
            if (isset($validated['completed'])) {
                if ($validated['completed']) {
                    $subtask->markAsCompleted();
                } else {
                    $subtask->markAsIncomplete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sous-tâche mise à jour avec succès',
                'subtask' => $subtask->fresh(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating subtask', [
                'subtask_id' => $subtask->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la sous-tâche',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Toggle the completion status of a subtask.
     */
    public function toggle(Subtask $subtask)
    {
        try {
            $subtask->toggleCompletion();

            return response()->json([
                'success' => true,
                'message' => $subtask->completed ? 'Sous-tâche marquée comme terminée' : 'Sous-tâche marquée comme non terminée',
                'subtask' => $subtask->fresh(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling subtask', [
                'subtask_id' => $subtask->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de statut',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Remove the specified subtask.
     */
    public function destroy(Subtask $subtask)
    {
        try {
            $subtask->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sous-tâche supprimée avec succès',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting subtask', [
                'subtask_id' => $subtask->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la sous-tâche',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Reorder subtasks.
     */
    public function reorder(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'subtasks' => 'required|array',
                'subtasks.*.id' => 'required|exists:subtasks,id',
                'subtasks.*.order' => 'required|integer|min:0',
            ]);

            foreach ($validated['subtasks'] as $subtaskData) {
                Subtask::where('id', $subtaskData['id'])
                    ->where('task_id', $task->id)
                    ->update(['order' => $subtaskData['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sous-tâches réorganisées avec succès',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error reordering subtasks', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réorganisation des sous-tâches',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }
}
