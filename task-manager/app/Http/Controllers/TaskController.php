<?php

namespace App\Http\Controllers;

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
        
        $query = Task::with(['context', 'user']);
        
        // Filtrer par semaine
        $query->forWeek($weekStart);
        
        // Filtrer par contexte si spécifié
        if ($request->has('context') && $request->context !== '') {
            $query->where('context_id', $request->context);
        }
        
        $tasks = $query->orderBy('priority', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        $contexts = Context::all();
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
            'contexts', 
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
        
        return view('tasks.create', compact('contexts', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:todo,in_progress,done',
            'priority' => 'in:low,medium,high,urgent',
            'context_id' => 'nullable|exists:contexts,id',
            'user_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'week_date' => 'nullable|date',
        ]);

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

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Tâche créée avec succès!');
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
        
        return view('tasks.edit', compact('task', 'contexts', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:todo,in_progress,done',
            'priority' => 'in:low,medium,high,urgent',
            'context_id' => 'nullable|exists:contexts,id',
            'user_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'week_date' => 'nullable|date',
        ]);

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
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $task->update($validated);

        return response()->json(['success' => true]);
    }
}
