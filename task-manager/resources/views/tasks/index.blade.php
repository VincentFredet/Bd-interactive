<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Tâches') }}
            </h2>
            <div class="flex space-x-2">
                @if(request('user') == auth()->id())
                    <a href="{{ route('tasks.index', request()->except('user')) }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded flex items-center">
                        📋 Toutes les tâches
                    </a>
                @else
                    <a href="{{ route('tasks.index', array_merge(request()->query(), ['user' => auth()->id()])) }}"
                       class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        👤 Mes tâches
                    </a>
                @endif
                <a href="{{ route('tasks.daily') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Vue Quotidienne
                </a>
                <a href="{{ route('contexts.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Nouveau Contexte
                </a>
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nouvelle Tâche
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Navigation par semaine -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">{{ $weekLabel }}</h3>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('tasks.index', array_merge(request()->query(), ['week' => $previousWeek->format('Y-m-d')])) }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                ← Semaine précédente
                            </a>
                            
                            @if(!$weekStart->isCurrentWeek())
                                <a href="{{ route('tasks.index', request()->except('week')) }}" 
                                   class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200">
                                    Cette semaine
                                </a>
                            @endif
                            
                            <a href="{{ route('tasks.index', array_merge(request()->query(), ['week' => $nextWeek->format('Y-m-d')])) }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Semaine suivante →
                            </a>
                        </div>
                    </div>
                    
                    <!-- Indicateur visuel de la semaine -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Du {{ $weekStart->format('l d F Y') }} au {{ $weekStart->copy()->endOfWeek()->format('l d F Y') }}
                        </div>
                        
                        <!-- Statistiques de la semaine -->
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="flex items-center space-x-1">
                                <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                                <span>{{ $weekStats['todo'] }} à faire</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="w-3 h-3 bg-blue-400 rounded-full"></span>
                                <span>{{ $weekStats['in_progress'] }} en cours</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                                <span>{{ $weekStats['done'] }} terminées</span>
                            </div>
                            <div class="text-gray-700 font-medium">
                                Total: {{ $weekStats['total'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Filtres</h3>
                        @if(request()->hasAny(['context', 'category', 'priority', 'status', 'user']))
                            <a href="{{ route('tasks.index', ['week' => request('week')]) }}"
                               class="text-sm text-red-600 hover:text-red-800 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Effacer les filtres
                            </a>
                        @endif
                    </div>

                    <!-- Contexte -->
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Contexte</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('tasks.index', array_merge(request()->except('context'), ['week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('context') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tous
                            </a>
                            @foreach($contexts as $context)
                                <a href="{{ route('tasks.index', array_merge(request()->except('context'), ['context' => $context->id, 'week' => request('week')])) }}"
                                   class="px-3 py-1 rounded-full text-xs font-medium text-white {{ request('context') == $context->id ? $context->button_active_class : $context->button_inactive_class }}">
                                    {{ $context->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Catégorie -->
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Catégorie</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('tasks.index', array_merge(request()->except('category'), ['week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('category') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Toutes
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('tasks.index', array_merge(request()->except('category'), ['category' => $category->id, 'week' => request('week')])) }}"
                                   class="px-3 py-1 rounded-full text-xs font-medium {{ request('category') == $category->id ? $category->badge_class : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Priorité -->
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Priorité</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('tasks.index', array_merge(request()->except('priority'), ['week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('priority') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Toutes
                            </a>
                            <a href="{{ route('tasks.index', array_merge(request()->except('priority'), ['priority' => 'low', 'week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'low' ? 'bg-gray-100 text-gray-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Basse
                            </a>
                            <a href="{{ route('tasks.index', array_merge(request()->except('priority'), ['priority' => 'medium', 'week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'medium' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Moyenne
                            </a>
                            <a href="{{ route('tasks.index', array_merge(request()->except('priority'), ['priority' => 'high', 'week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'high' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Haute
                            </a>
                            <a href="{{ route('tasks.index', array_merge(request()->except('priority'), ['priority' => 'urgent', 'week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Urgente
                            </a>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Statut</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('tasks.index', array_merge(request()->except('status'), ['week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('status') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tous
                            </a>
                            <a href="{{ route('tasks.index', array_merge(request()->except('status'), ['status' => 'todo', 'week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'todo' ? 'bg-gray-100 text-gray-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                À faire
                            </a>
                            <a href="{{ route('tasks.index', array_merge(request()->except('status'), ['status' => 'in_progress', 'week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                En cours
                            </a>
                            <a href="{{ route('tasks.index', array_merge(request()->except('status'), ['status' => 'done', 'week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'done' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Terminé
                            </a>
                        </div>
                    </div>

                    <!-- Utilisateur -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Assigné à</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('tasks.index', array_merge(request()->except('user'), ['week' => request('week')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('user') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tous
                            </a>
                            @foreach($users as $user)
                                <a href="{{ route('tasks.index', array_merge(request()->except('user'), ['user' => $user->id, 'week' => request('week')])) }}"
                                   class="px-3 py-1 rounded-full text-xs font-medium {{ request('user') == $user->id ? 'bg-purple-100 text-purple-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    {{ $user->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille hebdomadaire (7 jours) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($tasks->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">Aucune tâche trouvée.</p>
                            <a href="{{ route('tasks.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Créer votre première tâche
                            </a>
                        </div>
                    @else
                        <!-- Grille de 7 colonnes -->
                        <div class="grid grid-cols-7 gap-4">
                            @foreach($weekDays as $dateKey => $dayData)
                                <div class="flex flex-col">
                                    <!-- En-tête du jour -->
                                    <div class="mb-3 sticky top-0 bg-white z-10 pb-2">
                                        <div class="flex items-center justify-between px-2">
                                            <div class="text-center flex-1">
                                                <div class="font-semibold text-gray-900 {{ $dayData['is_today'] ? 'text-blue-600' : '' }}">
                                                    {{ $dayData['short_label'] }}
                                                </div>
                                                <div class="text-2xl font-bold {{ $dayData['is_today'] ? 'text-blue-600' : 'text-gray-400' }}">
                                                    {{ $dayData['day_number'] }}
                                                </div>
                                            </div>
                                            <button onclick="openQuickCreate('{{ $dateKey }}')"
                                                    class="flex-shrink-0 w-7 h-7 rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-colors"
                                                    title="Créer une tâche">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Zone de drop pour ce jour -->
                                    <div class="day-column min-h-[400px] bg-gray-50 rounded-lg p-2 space-y-2"
                                         data-date="{{ $dateKey }}"
                                         ondragover="handleDragOver(event)"
                                         ondrop="handleDrop(event)"
                                         ondragleave="handleDragLeave(event)">

                                        @forelse($dayData['tasks'] as $task)
                                            <!-- Carte de tâche draggable -->
                                            <div class="task-card bg-white border-l-4 {{ $task->context ? $task->context->border_class : 'border-l-4 border-gray-500' }} rounded-lg p-3 shadow-sm hover:shadow-md transition-all cursor-move"
                                                 draggable="true"
                                                 data-task-id="{{ $task->id }}"
                                                 ondragstart="handleDragStart(event)"
                                                 ondragend="handleDragEnd(event)"
                                                 onclick="openQuickEdit({{ $task->id }})">

                                                <!-- Titre et priorité -->
                                                <div class="flex items-start justify-between mb-2">
                                                    <h4 class="font-semibold text-sm text-gray-900 line-clamp-2 flex-1">
                                                        @if($task->is_recurring_template)
                                                            <span class="text-indigo-600" title="{{ $task->recurrence_description }}">🔄</span>
                                                        @elseif($task->is_recurring_instance)
                                                            <span class="text-gray-400" title="Instance d'une tâche récurrente">↻</span>
                                                        @endif
                                                        {{ $task->title }}
                                                    </h4>
                                                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $task->priority_badge_class }} flex-shrink-0">
                                                        {{ substr(ucfirst($task->priority), 0, 1) }}
                                                    </span>
                                                </div>

                                                <!-- Contexte et catégories -->
                                                <div class="flex flex-wrap gap-1 mb-2">
                                                    @if($task->context)
                                                        <span class="px-1.5 py-0.5 text-xs rounded {{ $task->context->badge_class }}">
                                                            {{ $task->context->name }}
                                                        </span>
                                                    @endif
                                                    @if($task->categories && $task->categories->isNotEmpty())
                                                        @foreach($task->categories->take(2) as $category)
                                                            <span class="px-1.5 py-0.5 text-xs rounded {{ $category->badge_class }}">
                                                                {{ $category->name }}
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <!-- Statut -->
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="px-2 py-0.5 rounded {{ $task->status_badge_class }}">
                                                        @if($task->status === 'todo') À faire
                                                        @elseif($task->status === 'in_progress') En cours
                                                        @else Terminé
                                                        @endif
                                                    </span>
                                                    @if($task->user)
                                                        <span class="text-gray-500 truncate ml-1">{{ substr($task->user->name, 0, 10) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center text-gray-400 text-sm py-4">
                                                Aucune tâche
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de création rapide de tâche -->
    <div id="quick-create-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Créer une tâche rapide</h3>
                <button onclick="closeQuickCreate()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="quick-create-form" action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="due_date" id="quick-due-date">

                <div class="mb-4">
                    <label for="quick-title" class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                    <input type="text" name="title" id="quick-title" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nom de la tâche">
                </div>

                <div class="mb-4">
                    <label for="quick-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="quick-description" rows="3"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Détails optionnels..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="quick-priority" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <select name="priority" id="quick-priority"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="low">Basse</option>
                            <option value="medium" selected>Moyenne</option>
                            <option value="high">Haute</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>

                    <div>
                        <label for="quick-status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="status" id="quick-status"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="todo" selected>À faire</option>
                            <option value="in_progress">En cours</option>
                            <option value="done">Terminé</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="quick-context" class="block text-sm font-medium text-gray-700 mb-1">Contexte</label>
                    <select name="context_id" id="quick-context"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Aucun contexte</option>
                        @foreach($contexts as $context)
                            <option value="{{ $context->id }}">{{ $context->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="quick-user" class="block text-sm font-medium text-gray-700 mb-1">Assigner à</label>
                    <select name="user_id" id="quick-user"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Non assigné</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeQuickCreate()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Créer la tâche
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mise à jour du statut de tâche
        function updateTaskStatus(taskId, status) {
            fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Statut mis à jour avec succès');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                location.reload();
            });
        }

        // Variables globales pour le drag & drop
        let draggedElement = null;
        let draggedTaskId = null;

        // Début du drag
        function handleDragStart(e) {
            draggedElement = e.currentTarget;
            draggedTaskId = e.currentTarget.getAttribute('data-task-id');
            e.currentTarget.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', e.currentTarget.innerHTML);
        }

        // Fin du drag
        function handleDragEnd(e) {
            e.currentTarget.style.opacity = '1';
            // Retirer les highlights
            document.querySelectorAll('.day-column').forEach(col => {
                col.classList.remove('bg-blue-100', 'border-2', 'border-blue-400', 'border-dashed');
            });
        }

        // Drag over une zone
        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';

            // Highlight la colonne
            const column = e.currentTarget;
            if (column.classList.contains('day-column')) {
                column.classList.add('bg-blue-100', 'border-2', 'border-blue-400', 'border-dashed');
            }
            return false;
        }

        // Quitter la zone de drag
        function handleDragLeave(e) {
            const column = e.currentTarget;
            if (column.classList.contains('day-column')) {
                column.classList.remove('bg-blue-100', 'border-2', 'border-blue-400', 'border-dashed');
            }
        }

        // Drop sur une colonne
        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            const column = e.currentTarget;
            column.classList.remove('bg-blue-100', 'border-2', 'border-blue-400', 'border-dashed');

            const newDate = column.getAttribute('data-date');

            if (draggedElement && draggedTaskId) {
                // Mise à jour via AJAX
                updateTaskDate(draggedTaskId, newDate);
            }

            return false;
        }

        // Mise à jour de la date d'une tâche
        function updateTaskDate(taskId, newDate) {
            fetch(`/tasks/${taskId}/due-date`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ due_date: newDate })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Date mise à jour avec succès');
                    // Recharger la page pour voir les changements
                    location.reload();
                } else {
                    alert('Erreur lors de la mise à jour de la date');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour de la date');
            });
        }

        // Édition rapide d'une tâche (clic)
        function openQuickEdit(taskId) {
            // Pour l'instant, on redirige vers la page d'édition
            // TODO: Implémenter un modal d'édition rapide plus tard
            window.location.href = `/tasks/${taskId}/edit`;
        }

        // Ouvrir le modal de création rapide
        function openQuickCreate(date) {
            // Pre-remplir la date avec le jour sélectionné
            document.getElementById('quick-due-date').value = date;
            // Réinitialiser les autres champs
            document.getElementById('quick-create-form').reset();
            // Re-définir la date après le reset
            document.getElementById('quick-due-date').value = date;
            // Afficher le modal
            document.getElementById('quick-create-modal').classList.remove('hidden');
            // Focus sur le champ titre
            setTimeout(() => {
                document.getElementById('quick-title').focus();
            }, 100);
        }

        // Fermer le modal de création rapide
        function closeQuickCreate() {
            // Masquer le modal
            document.getElementById('quick-create-modal').classList.add('hidden');
            // Réinitialiser le formulaire
            document.getElementById('quick-create-form').reset();
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('quick-create-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickCreate();
            }
        });
    </script>
</x-app-layout>