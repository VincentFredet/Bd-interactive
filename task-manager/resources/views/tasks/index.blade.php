<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Tâches') }}
            </h2>
            <div class="flex space-x-2">
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

            <!-- Filtres par contexte -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filtrer par contexte</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('tasks.index') }}"
                           class="px-4 py-2 rounded-full text-sm font-medium {{ !request('context') ? 'bg-blue-500 text-white hover:bg-blue-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Tous
                        </a>
                        @foreach($contexts as $context)
                            <a href="{{ route('tasks.index', ['context' => $context->id]) }}"
                               class="px-4 py-2 rounded-full text-sm font-medium text-white {{ request('context') == $context->id ? $context->button_active_class : $context->button_inactive_class }}">
                                {{ $context->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Liste des tâches -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($tasks->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">Aucune tâche trouvée.</p>
                            <a href="{{ route('tasks.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Créer votre première tâche
                            </a>
                        </div>
                    @else
                        <div class="grid gap-4">
                            @foreach($tasks as $task)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow {{ $task->context ? $task->context->border_class : '' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $task->priority_badge_class }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </div>
                                            
                                            @if($task->description)
                                                <p class="text-gray-600 mb-2">{{ Str::limit($task->description, 100) }}</p>
                                            @endif
                                            
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                @if($task->context)
                                                    <span class="px-2 py-1 rounded {{ $task->context->badge_class }}">{{ $task->context->name }}</span>
                                                @endif
                                                @if($task->user)
                                                    <span>Assigné à: {{ $task->user->name }}</span>
                                                @endif
                                                <span>{{ $task->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if($task->image)
                                                <img src="{{ Storage::url('tasks/' . $task->image) }}" 
                                                     alt="Image de la tâche" 
                                                     class="w-16 h-16 object-cover rounded">
                                            @endif
                                            
                                            <div class="flex flex-col space-y-2">
                                                <select onchange="updateTaskStatus({{ $task->id }}, this.value)" 
                                                        class="text-sm border-gray-300 rounded-md">
                                                    <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>À faire</option>
                                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Terminé</option>
                                                </select>
                                                
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

    <script>
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
                    // Optionnel: afficher un message de succès
                    console.log('Statut mis à jour avec succès');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Recharger la page en cas d'erreur
                location.reload();
            });
        }
    </script>
</x-app-layout>