<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Context;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Déterminer la semaine à afficher
        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY)
            : Task::getWeekStart();

        $query = Task::with(['context', 'user', 'categories']);
        
        // Filtrer par semaine
        $query->forWeek($weekStart);

        // Filtrer par contexte si spécifié
        if ($request->has('context') && $request->context !== '') {
            $query->where('context_id', $request->context);
        }

        // Filtrer par catégorie si spécifiée
        if ($request->has('category') && $request->category !== '') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Filtrer par priorité si spécifiée
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filtrer par statut si spécifié
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filtrer par utilisateur si spécifié
        if ($request->has('user') && $request->user !== '') {
            $query->where('user_id', $request->user);
        }

        $tasks = $query->orderBy('priority', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->get();

        // Grouper les tâches par jour de la semaine
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $weekDays[$day->format('Y-m-d')] = [
                'date' => $day,
                'label' => ucfirst($day->translatedFormat('l')),
                'short_label' => ucfirst($day->translatedFormat('D')),
                'day_number' => $day->format('d'),
                'is_today' => $day->isToday(),
                'tasks' => $tasks->filter(function($task) use ($day) {
                    return $task->due_date && $task->due_date->isSameDay($day);
                })->values()
            ];
        }

        $contexts = Context::all();
        $categories = \App\Models\Category::all();
        $users = User::all();
        
        // Calculer les semaines pour la navigation
        $currentWeek = Task::getWeekStart();
        $previousWeek = $currentWeek->copy()->subWeek();
        $nextWeek = $currentWeek->copy()->addWeek();
        
        // Libellé de la semaine actuelle
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $weekLabel = $weekStart->isCurrentWeek() 
            ? 'Cette semaine' 
            : 'Semaine du ' . $weekStart->format('d/m') . ' au ' . $weekEnd->format('d/m');
        
        // Statistiques de la semaine
        $weekStats = [
            'total' => $tasks->count(),
            'todo' => $tasks->where('status', 'todo')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'done' => $tasks->where('status', 'done')->count(),
        ];
        
        return view('tasks.index', compact(
            'tasks',
            'weekDays',
            'contexts',
            'categories',
            'users',
            'weekStart',
            'previousWeek',
            'nextWeek',
            'currentWeek',
            'weekLabel',
            'weekStats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contexts = Context::all();
        $users = User::all();
        $categories = \App\Models\Category::all();

        return view('tasks.create', compact('contexts', 'users', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();

        // Si aucune semaine n'est spécifiée, utiliser la semaine courante
        if (!isset($validated['week_date'])) {
            $validated['week_date'] = Task::getWeekStart();
        } else {
            // S'assurer que la date est le début de semaine
            $validated['week_date'] = Task::getWeekStart($validated['week_date']);
        }

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('tasks', $imageName, 'public');
            $validated['image'] = $imageName;
        }

        // Gérer la récurrence
        if ($request->has('is_recurring') && $request->is_recurring) {
            $validated['is_recurring'] = true;
            $validated['recurrence_pattern'] = [
                'type' => $request->recurrence_type ?? 'weekly',
                'interval' => (int) ($request->recurrence_interval ?? 1),
            ];

            if ($request->filled('recurrence_end_date')) {
                $validated['recurrence_pattern']['end_date'] = $request->recurrence_end_date;
            }
        } else {
            $validated['is_recurring'] = false;
            $validated['recurrence_pattern'] = null;
        }

        $task = Task::create($validated);

        // Synchroniser les catégories
        if ($request->has('categories')) {
            $task->categories()->sync($request->categories);
        }

        $message = $task->is_recurring
            ? 'Tâche récurrente créée avec succès! Les instances seront générées automatiquement.'
            : 'Tâche créée avec succès!';

        return redirect()->route('tasks.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['context', 'user']);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $contexts = Context::all();
        $users = User::all();
        $categories = \App\Models\Category::all();
        $task->load('categories');

        return view('tasks.edit', compact('task', 'contexts', 'users', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validated = $request->validated();

        // Si une nouvelle semaine est spécifiée, s'assurer que c'est le début de semaine
        if (isset($validated['week_date'])) {
            $validated['week_date'] = Task::getWeekStart($validated['week_date']);
        }

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($task->image) {
                Storage::disk('public')->delete('tasks/' . $task->image);
            }
            
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('tasks', $imageName, 'public');
            $validated['image'] = $imageName;
        }

        $task->update($validated);

        // Synchroniser les catégories
        if ($request->has('categories')) {
            $task->categories()->sync($request->categories);
        } else {
            $task->categories()->detach();
        }

        return redirect()->route('tasks.index')->with('success', 'Tâche mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Supprimer l'image si elle existe
        if ($task->image) {
            Storage::disk('public')->delete('tasks/' . $task->image);
        }
        
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée avec succès!');
    }

    /**
     * Update task status via AJAX
     */
    public function updateStatus(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:todo,in_progress,done',
            ]);

            $oldStatus = $task->status;
            $task->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'old_status' => $oldStatus,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating task status', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Update task due date via AJAX (for drag & drop)
     */
    public function updateDueDate(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'due_date' => 'required|date',
            ]);

            $task->update(['due_date' => $validated['due_date']]);

            return response()->json([
                'success' => true,
                'message' => 'Date mise à jour avec succès',
                'task' => [
                    'id' => $task->id,
                    'due_date' => $task->due_date->format('Y-m-d'),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating task due date', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la date',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Vue quotidienne des tâches
     */
    public function daily(Request $request)
    {
        // Déterminer le jour à afficher
        $currentDate = $request->has('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        $query = Task::with(['context', 'user', 'categories']);
        
        // Filtrer par date d'échéance
        $query->forDate($currentDate);

        // Filtrer par contexte si spécifié
        if ($request->has('context') && $request->context !== '') {
            $query->where('context_id', $request->context);
        }

        // Filtrer par catégorie si spécifiée
        if ($request->has('category') && $request->category !== '') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Filtrer par priorité si spécifiée
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filtrer par statut si spécifié
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filtrer par utilisateur si spécifié
        if ($request->has('user') && $request->user !== '') {
            $query->where('user_id', $request->user);
        }

        $tasks = $query->orderBy('priority', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->get();

        // Tâches en retard (seulement si on regarde aujourd'hui)
        $overdueTasks = collect();
        if ($currentDate->isToday()) {
            $overdueTasks = Task::with(['context', 'user', 'categories'])
                               ->overdue()
                               ->orderBy('due_date', 'asc')
                               ->get();
        }

        $contexts = Context::all();
        $categories = \App\Models\Category::all();
        $users = User::all();
        
        // Navigation par jour
        $previousDay = $currentDate->copy()->subDay();
        $nextDay = $currentDate->copy()->addDay();
        $today = Carbon::today();
        
        // Libellé du jour
        $dayLabel = $currentDate->isToday() 
            ? 'Aujourd\'hui' 
            : $currentDate->format('l d F Y');
        
        // Statistiques du jour
        $dayStats = [
            'total' => $tasks->count(),
            'todo' => $tasks->where('status', 'todo')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'done' => $tasks->where('status', 'done')->count(),
            'overdue' => $overdueTasks->count(),
        ];
        
        return view('tasks.daily', compact(
            'tasks',
            'overdueTasks',
            'contexts',
            'categories',
            'users',
            'currentDate', 
            'previousDay', 
            'nextDay', 
            'today',
            'dayLabel',
            'dayStats'
        ));
    }

    /**
     * Marquer une tâche comme terminée
     */
    public function complete(Task $task)
    {
        try {
            if ($task->status === 'done') {
                return response()->json([
                    'success' => false,
                    'message' => 'La tâche est déjà terminée',
                ], 422);
            }

            $task->markAsCompleted();

            return response()->json([
                'success' => true,
                'message' => 'Tâche marquée comme terminée!',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'completed_at' => $task->completed_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error completing task', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la complétion de la tâche',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Reporter une tâche
     */
    public function postpone(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|after_or_equal:today',
            ], [
                'date.required' => 'La date est obligatoire',
                'date.date' => 'La date n\'est pas valide',
                'date.after_or_equal' => 'La date ne peut pas être dans le passé',
            ]);

            $oldDueDate = $task->due_date;
            $task->postponeTo($validated['date']);

            return response()->json([
                'success' => true,
                'message' => 'Tâche reportée avec succès!',
                'task' => [
                    'id' => $task->id,
                    'due_date' => $task->due_date->format('Y-m-d'),
                    'old_due_date' => $oldDueDate?->format('Y-m-d'),
                    'status' => $task->status,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error postponing task', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du report de la tâche',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], 500);
        }
    }
}
