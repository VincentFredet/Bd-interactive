<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    /**
     * Display a listing of comments for a task.
     */
    public function index(Task $task)
    {
        try {
            $comments = $task->comments()
                ->with('user')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'comments' => $comments,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching task comments', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des commentaires',
            ], 500);
        }
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|max:5000',
            ], [
                'content.required' => 'Le contenu du commentaire est obligatoire',
                'content.max' => 'Le commentaire ne peut pas dépasser 5000 caractères',
            ]);

            $comment = $task->comments()->create([
                'user_id' => auth()->id(),
                'content' => $validated['content'],
            ]);

            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Commentaire ajouté avec succès',
                'comment' => $comment,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating task comment', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du commentaire',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, TaskComment $comment)
    {
        try {
            // Check if user owns the comment
            if ($comment->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à modifier ce commentaire',
                ], 403);
            }

            $validated = $request->validate([
                'content' => 'required|string|max:5000',
            ], [
                'content.required' => 'Le contenu du commentaire est obligatoire',
                'content.max' => 'Le commentaire ne peut pas dépasser 5000 caractères',
            ]);

            $comment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Commentaire mis à jour avec succès',
                'comment' => $comment->fresh()->load('user'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating task comment', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du commentaire',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(TaskComment $comment)
    {
        try {
            // Check if user owns the comment
            if ($comment->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire',
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Commentaire supprimé avec succès',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting task comment', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du commentaire',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }
}
