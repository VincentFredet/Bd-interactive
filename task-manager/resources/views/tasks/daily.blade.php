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

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Filtres</h3>
                        @if(request()->hasAny(['context', 'category', 'priority', 'status', 'user']))
                            <a href="{{ route('tasks.daily', ['date' => $currentDate->format('Y-m-d')]) }}"
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
                            <a href="{{ route('tasks.daily', array_merge(request()->except('context'), ['date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('context') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tous
                            </a>
                            @foreach($contexts as $context)
                                <a href="{{ route('tasks.daily', array_merge(request()->except('context'), ['context' => $context->id, 'date' => $currentDate->format('Y-m-d')])) }}"
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
                            <a href="{{ route('tasks.daily', array_merge(request()->except('category'), ['date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('category') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Toutes
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('tasks.daily', array_merge(request()->except('category'), ['category' => $category->id, 'date' => $currentDate->format('Y-m-d')])) }}"
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
                            <a href="{{ route('tasks.daily', array_merge(request()->except('priority'), ['date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('priority') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Toutes
                            </a>
                            <a href="{{ route('tasks.daily', array_merge(request()->except('priority'), ['priority' => 'low', 'date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'low' ? 'bg-gray-100 text-gray-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Basse
                            </a>
                            <a href="{{ route('tasks.daily', array_merge(request()->except('priority'), ['priority' => 'medium', 'date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'medium' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Moyenne
                            </a>
                            <a href="{{ route('tasks.daily', array_merge(request()->except('priority'), ['priority' => 'high', 'date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'high' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Haute
                            </a>
                            <a href="{{ route('tasks.daily', array_merge(request()->except('priority'), ['priority' => 'urgent', 'date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('priority') === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Urgente
                            </a>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Statut</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('tasks.daily', array_merge(request()->except('status'), ['date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('status') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tous
                            </a>
                            <a href="{{ route('tasks.daily', array_merge(request()->except('status'), ['status' => 'todo', 'date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'todo' ? 'bg-gray-100 text-gray-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                À faire
                            </a>
                            <a href="{{ route('tasks.daily', array_merge(request()->except('status'), ['status' => 'in_progress', 'date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                En cours
                            </a>
                            <a href="{{ route('tasks.daily', array_merge(request()->except('status'), ['status' => 'done', 'date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'done' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Terminé
                            </a>
                        </div>
                    </div>

                    <!-- Utilisateur -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Assigné à</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('tasks.daily', array_merge(request()->except('user'), ['date' => $currentDate->format('Y-m-d')])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium {{ !request('user') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tous
                            </a>
                            @foreach($users as $user)
                                <a href="{{ route('tasks.daily', array_merge(request()->except('user'), ['user' => $user->id, 'date' => $currentDate->format('Y-m-d')])) }}"
                                   class="px-3 py-1 rounded-full text-xs font-medium {{ request('user') == $user->id ? 'bg-purple-100 text-purple-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    {{ $user->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
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