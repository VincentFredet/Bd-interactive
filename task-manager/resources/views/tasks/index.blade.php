<x-app-layout>
    @php
        // Définir le thème de couleur basé sur le contexte sélectionné
        $themeColor = $selectedContext ? $selectedContext->color : 'blue';
        $themeBg = 'bg-' . $themeColor . '-500';
        $themeBgHover = 'bg-' . $themeColor . '-600';
        $themeBgLight = 'bg-' . $themeColor . '-100';
        $themeText = 'text-' . $themeColor . '-600';
        $themeBorder = 'border-' . $themeColor . '-500';
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <!-- Filtre des contextes dans le header -->
            <div class="flex items-center gap-2">
                <span class="font-semibold text-lg text-gray-800 mr-2">Contexte:</span>
                <a href="{{ route('tasks.index', array_merge(request()->except('context'), ['week' => request('week')])) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-colors {{ !request('context') ? 'bg-gray-700 text-white' : 'bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                    Tous
                </a>
                @foreach($contexts as $context)
                    <a href="{{ route('tasks.index', array_merge(request()->except('context'), ['context' => $context->id, 'week' => request('week')])) }}"
                       class="px-4 py-2 rounded-lg font-medium transition-colors {{ request('context') == $context->id ? ($context->button_active_class ?: '') : ($context->button_inactive_class ?: 'bg-white border-2 border-gray-300 hover:bg-gray-100') }}"
                       style="{{ request('context') == $context->id ? $context->button_active_style : $context->button_inactive_style }}">
                        {{ $context->name }}
                    </a>
                @endforeach
            </div>
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
                <a href="{{ route('tasks.daily') }}" class="{{ $themeBg }} hover:{{ $themeBgHover }} text-white font-bold py-2 px-4 rounded transition-colors">
                    Vue Quotidienne
                </a>
                <a href="{{ route('contexts.create') }}" class="{{ $themeBg }} hover:{{ $themeBgHover }} text-white font-bold py-2 px-4 rounded transition-colors">
                    Nouveau Contexte
                </a>
                <a href="{{ route('tasks.create') }}" class="{{ $themeBg }} hover:{{ $themeBgHover }} text-white font-bold py-2 px-4 rounded transition-colors">
                    Nouvelle Tâche
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
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

            <!-- Filtres compacts -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-base font-medium text-gray-900">Filtres</h3>
                        @if(request()->hasAny(['category', 'priority', 'status', 'user']))
                            <a href="{{ route('tasks.index', ['week' => request('week'), 'context' => request('context')]) }}"
                               class="text-xs text-red-600 hover:text-red-800 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Réinitialiser
                            </a>
                        @endif
                    </div>

                    <!-- Tous les filtres en dropdowns sur une seule ligne -->
                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Catégorie -->
                        <div class="flex items-center gap-2">
                            <label for="filter-category" class="font-semibold text-gray-700 text-sm">Catégorie:</label>
                            <select id="filter-category"
                                    onchange="updateFilter('category', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-{{ $themeColor }}-500 focus:border-{{ $themeColor }}-500">
                                <option value="">Toutes</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Priorité -->
                        <div class="flex items-center gap-2">
                            <label for="filter-priority" class="font-semibold text-gray-700 text-sm">Priorité:</label>
                            <select id="filter-priority"
                                    onchange="updateFilter('priority', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-{{ $themeColor }}-500 focus:border-{{ $themeColor }}-500">
                                <option value="">Toutes</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="flex items-center gap-2">
                            <label for="filter-status" class="font-semibold text-gray-700 text-sm">Statut:</label>
                            <select id="filter-status"
                                    onchange="updateFilter('status', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-{{ $themeColor }}-500 focus:border-{{ $themeColor }}-500">
                                <option value="">Tous</option>
                                <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>À faire</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Terminé</option>
                            </select>
                        </div>

                        <!-- Utilisateur -->
                        <div class="flex items-center gap-2">
                            <label for="filter-user" class="font-semibold text-gray-700 text-sm">Assigné:</label>
                            <select id="filter-user"
                                    onchange="updateFilter('user', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-{{ $themeColor }}-500 focus:border-{{ $themeColor }}-500">
                                <option value="">Tous</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <script>
                        function updateFilter(filterName, filterValue) {
                            const url = new URL(window.location.href);

                            // Préserver tous les paramètres existants
                            const params = new URLSearchParams(url.search);

                            // Mettre à jour ou supprimer le filtre
                            if (filterValue) {
                                params.set(filterName, filterValue);
                            } else {
                                params.delete(filterName);
                            }

                            // Rediriger avec tous les paramètres
                            window.location.href = url.pathname + '?' + params.toString();
                        }
                    </script>
                </div>
            </div>

            <!-- Grille hebdomadaire (7 jours) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    @if($tasks->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">Aucune tâche trouvée.</p>
                            <a href="{{ route('tasks.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Créer votre première tâche
                            </a>
                        </div>
                    @else
                        <!-- Grille de 7 colonnes pour les 7 jours de la semaine -->
                        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.75rem;">
                            @foreach($weekDays as $dateKey => $dayData)
                                <div style="display: flex; flex-direction: column; min-width: 0;">
                                    <!-- En-tête du jour -->
                                    <div class="mb-3 sticky top-0 bg-white z-10 pb-2">
                                        <div class="flex items-center justify-between px-2">
                                            <div class="text-center flex-1">
                                                <div class="font-semibold text-gray-900 {{ $dayData['is_today'] ? $themeText : '' }}">
                                                    {{ $dayData['short_label'] }}
                                                </div>
                                                <div class="text-2xl font-bold {{ $dayData['is_today'] ? $themeText : 'text-gray-400' }}">
                                                    {{ $dayData['day_number'] }}
                                                </div>
                                            </div>
                                            <button onclick="openQuickCreate('{{ $dateKey }}')"
                                                    class="flex-shrink-0 w-7 h-7 rounded-full {{ $themeBg }} hover:{{ $themeBgHover }} text-white flex items-center justify-center transition-colors"
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
                                            <div class="task-card bg-white {{ $task->context ? $task->context->border_class : 'border-l-4 border-gray-500' }} rounded-lg p-3 shadow-sm hover:shadow-md transition-all cursor-move"
                                                 style="{{ $task->context ? $task->context->border_style : '' }}"
                                                 draggable="true"
                                                 data-task-id="{{ $task->id }}"
                                                 ondragstart="handleDragStart(event)"
                                                 ondragend="handleDragEnd(event)"
                                                 onclick="openQuickEdit({{ $task->id }})">

                                                <!-- Titre et priorité -->
                                                <div class="flex items-start justify-between mb-2">
                                                    <div class="flex items-start gap-2 flex-1">
                                                        <input type="checkbox"
                                                               {{ $task->status === 'done' ? 'checked' : '' }}
                                                               onclick="event.stopPropagation(); toggleTaskCompletion({{ $task->id }}, this.checked)"
                                                               class="mt-0.5 w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 cursor-pointer flex-shrink-0"
                                                               title="Marquer comme terminée">
                                                        <h4 class="font-semibold text-sm text-gray-900 line-clamp-2 flex-1 {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                                            @if($task->is_recurring_template)
                                                                <span class="text-indigo-600" title="{{ $task->recurrence_description }}">🔄</span>
                                                            @elseif($task->is_recurring_instance)
                                                                <span class="text-gray-400" title="Instance d'une tâche récurrente">↻</span>
                                                            @endif
                                                            {{ $task->title }}
                                                        </h4>
                                                    </div>
                                                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $task->priority_badge_class }} flex-shrink-0">
                                                        {{ substr(ucfirst($task->priority), 0, 1) }}
                                                    </span>
                                                </div>

                                                <!-- Contexte et catégories -->
                                                <div class="flex flex-wrap gap-1 mb-2">
                                                    @if($task->context)
                                                        <span class="px-1.5 py-0.5 text-xs rounded {{ $task->context->badge_class }}" style="{{ $task->context->badge_style }}">
                                                            {{ $task->context->name }}
                                                        </span>
                                                    @endif
                                                    @if($task->categories && $task->categories->isNotEmpty())
                                                        @foreach($task->categories->take(2) as $category)
                                                            <span class="px-1.5 py-0.5 text-xs rounded {{ $category->badge_class }}" style="{{ $category->badge_style }}">
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
            <div class="flex justify-between items-center mb-4 px-2 pt-2">
                <h3 class="text-lg font-medium text-gray-900">Créer une tâche rapide</h3>
                <button onclick="closeQuickCreate()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="quick-create-form" action="{{ route('tasks.store') }}" method="POST" class="px-2 pb-2">
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

        // Toggle task completion via checkbox
        function toggleTaskCompletion(taskId, isChecked) {
            const newStatus = isChecked ? 'done' : 'todo';

            fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page to update the UI
                    location.reload();
                } else {
                    alert('Erreur lors de la mise à jour du statut');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour du statut');
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