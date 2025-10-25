<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion Quotidienne') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Vue Semaine
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

            <!-- Navigation par jour -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">{{ $dayLabel }}</h3>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('tasks.daily', array_merge(request()->query(), ['date' => $previousDay->format('Y-m-d')])) }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                ← Jour précédent
                            </a>
                            
                            @if(!$currentDate->isToday())
                                <a href="{{ route('tasks.daily', request()->except('date')) }}" 
                                   class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200">
                                    Aujourd'hui
                                </a>
                            @endif
                            
                            <a href="{{ route('tasks.daily', array_merge(request()->query(), ['date' => $nextDay->format('Y-m-d')])) }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Jour suivant →
                            </a>
                        </div>
                    </div>
                    
                    <!-- Indicateur visuel du jour -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            {{ $currentDate->format('l d F Y') }}
                        </div>
                        
                        <!-- Statistiques du jour -->
                        <div class="flex items-center space-x-4 text-sm">
                            @if($dayStats['overdue'] > 0)
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                    <span class="text-red-600 font-medium">{{ $dayStats['overdue'] }} en retard</span>
                                </div>
                            @endif
                            <div class="flex items-center space-x-1">
                                <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                                <span>{{ $dayStats['todo'] }} à faire</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="w-3 h-3 bg-blue-400 rounded-full"></span>
                                <span>{{ $dayStats['in_progress'] }} en cours</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                                <span>{{ $dayStats['done'] }} terminées</span>
                            </div>
                            <div class="text-gray-700 font-medium">
                                Total: {{ $dayStats['total'] }}
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
                        @if(request()->hasAny(['context', 'category', 'priority', 'status', 'user']))
                            <a href="{{ route('tasks.daily', ['date' => $currentDate->format('Y-m-d')]) }}"
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
                        <!-- Contexte -->
                        <div class="flex items-center gap-2">
                            <label for="filter-context-daily" class="font-semibold text-gray-700 text-sm">Contexte:</label>
                            <select id="filter-context-daily"
                                    onchange="updateFilterDaily('context', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous</option>
                                @foreach($contexts as $context)
                                    <option value="{{ $context->id }}" {{ request('context') == $context->id ? 'selected' : '' }}>
                                        {{ $context->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Catégorie -->
                        <div class="flex items-center gap-2">
                            <label for="filter-category-daily" class="font-semibold text-gray-700 text-sm">Catégorie:</label>
                            <select id="filter-category-daily"
                                    onchange="updateFilterDaily('category', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
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
                            <label for="filter-priority-daily" class="font-semibold text-gray-700 text-sm">Priorité:</label>
                            <select id="filter-priority-daily"
                                    onchange="updateFilterDaily('priority', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Toutes</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="flex items-center gap-2">
                            <label for="filter-status-daily" class="font-semibold text-gray-700 text-sm">Statut:</label>
                            <select id="filter-status-daily"
                                    onchange="updateFilterDaily('status', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous</option>
                                <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>À faire</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Terminé</option>
                            </select>
                        </div>

                        <!-- Utilisateur -->
                        <div class="flex items-center gap-2">
                            <label for="filter-user-daily" class="font-semibold text-gray-700 text-sm">Assigné:</label>
                            <select id="filter-user-daily"
                                    onchange="updateFilterDaily('user', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
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
                        function updateFilterDaily(filterName, filterValue) {
                            const url = new URL(window.location.href);
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

            <!-- Tâches en retard (seulement pour aujourd'hui) -->
            @if($overdueTasks->isNotEmpty())
                <div class="bg-red-50 border border-red-200 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-red-900 mb-4">⚠️ Tâches en retard</h3>
                        <div class="grid gap-3">
                            @foreach($overdueTasks as $task)
                                <div class="bg-white border border-red-200 rounded-lg p-4 {{ $task->context ? $task->context->border_class : '' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h4 class="font-semibold text-gray-900">{{ $task->title }}</h4>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $task->priority_badge_class }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    Retard: {{ $task->due_date->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                @if($task->context)
                                                    <span class="px-2 py-1 rounded {{ $task->context->badge_class }}">{{ $task->context->name }}</span>
                                                @endif
                                                @if($task->categories && $task->categories->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($task->categories as $category)
                                                            <span class="px-2 py-1 text-xs rounded {{ $category->badge_class }}">{{ $category->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($task->user)
                                                    <span>{{ $task->user->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="completeTask({{ $task->id }})" 
                                                    class="px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                                ✓ Terminer
                                            </button>
                                            <button onclick="postponeTask({{ $task->id }})" 
                                                    class="px-3 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                📅 Reporter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tâches du jour -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($tasks->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">
                                @if($currentDate->isToday())
                                    Aucune tâche pour aujourd'hui ! 🎉
                                @else
                                    Aucune tâche pour ce jour.
                                @endif
                            </p>
                            <a href="{{ route('tasks.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Ajouter une tâche
                            </a>
                        </div>
                    @else
                        <div class="grid gap-4">
                            @foreach($tasks as $task)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow task-item {{ $task->context ? $task->context->border_class : '' }}" data-task-id="{{ $task->id }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3 flex-1">
                                            <!-- Checkbox pour cocher rapidement -->
                                            <input type="checkbox" 
                                                   {{ $task->status === 'done' ? 'checked' : '' }}
                                                   onchange="toggleTaskComplete({{ $task->id }}, this.checked)"
                                                   class="mt-1 h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                            
                                            <div class="flex-1 {{ $task->status === 'done' ? 'opacity-50' : '' }}">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <h3 class="text-lg font-semibold text-gray-900 {{ $task->status === 'done' ? 'line-through' : '' }}">
                                                        {{ $task->title }}
                                                    </h3>
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $task->priority_badge_class }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $task->status_badge_class }}">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                </div>
                                                
                                                @if($task->description)
                                                    <p class="text-gray-600 mb-2">{{ Str::limit($task->description, 100) }}</p>
                                                @endif
                                                
                                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                    @if($task->context)
                                                        <span class="px-2 py-1 rounded {{ $task->context->badge_class }}">{{ $task->context->name }}</span>
                                                    @endif
                                                    @if($task->categories && $task->categories->isNotEmpty())
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($task->categories as $category)
                                                                <span class="px-2 py-1 text-xs rounded {{ $category->badge_class }}">{{ $category->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if($task->user)
                                                        <span>Assigné à: {{ $task->user->name }}</span>
                                                    @endif
                                                    @if($task->completed_at)
                                                        <span class="text-green-600">Terminé: {{ $task->completed_at->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if($task->image)
                                                <img src="{{ Storage::url('tasks/' . $task->image) }}" 
                                                     alt="Image de la tâche" 
                                                     class="w-16 h-16 object-cover rounded">
                                            @endif
                                            
                                            <div class="flex flex-col space-y-2">
                                                @if($task->status !== 'done')
                                                    <button onclick="postponeTask({{ $task->id }})" 
                                                            class="px-3 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                        📅 Reporter
                                                    </button>
                                                @endif
                                                
                                                <div class="flex space-x-1">
                                                    <a href="{{ route('tasks.edit', $task) }}" 
                                                       class="text-blue-600 hover:text-blue-900 text-sm">Modifier</a>
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')"
                                                                class="text-red-600 hover:text-red-900 text-sm">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour reporter une tâche -->
    <div id="postponeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reporter la tâche</h3>
            <form id="postponeForm">
                <div class="mb-4">
                    <label for="postponeDate" class="block text-sm font-medium text-gray-700 mb-2">Nouvelle date d'échéance</label>
                    <input type="date" id="postponeDate" name="date" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closePostponeModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-md hover:bg-yellow-600">
                        Reporter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentTaskId = null;

        function toggleTaskComplete(taskId, isCompleted) {
            if (isCompleted) {
                completeTask(taskId);
            } else {
                // Remettre la tâche en "todo"
                updateTaskStatus(taskId, 'todo');
            }
        }

        function completeTask(taskId) {
            fetch(`/tasks/${taskId}/complete`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recharger pour voir les changements
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                location.reload();
            });
        }

        function postponeTask(taskId) {
            currentTaskId = taskId;
            document.getElementById('postponeDate').value = '{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}';
            document.getElementById('postponeModal').classList.remove('hidden');
            document.getElementById('postponeModal').classList.add('flex');
        }

        function closePostponeModal() {
            document.getElementById('postponeModal').classList.add('hidden');
            document.getElementById('postponeModal').classList.remove('flex');
            currentTaskId = null;
        }

        document.getElementById('postponeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const date = document.getElementById('postponeDate').value;
            
            fetch(`/tasks/${currentTaskId}/postpone`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ date: date })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closePostponeModal();
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                closePostponeModal();
                location.reload();
            });
        });

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
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                location.reload();
            });
        }
    </script>
</x-app-layout>