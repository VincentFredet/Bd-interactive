<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier la tâche') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Titre -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Titre *</label>
                                <input type="text" name="title" id="title" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('title', $task->title) }}">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $task->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                                <select name="status" id="status"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="todo" {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>À faire</option>
                                    <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                    <option value="done" {{ old('status', $task->status) === 'done' ? 'selected' : '' }}>Terminé</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priorité -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priorité</label>
                                <select name="priority" id="priority"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Faible</option>
                                    <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                    <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>Élevée</option>
                                    <option value="urgent" {{ old('priority', $task->priority) === 'urgent' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contexte -->
                            <div>
                                <label for="context_id" class="block text-sm font-medium text-gray-700">Contexte</label>
                                <select name="context_id" id="context_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Aucun contexte</option>
                                    @foreach($contexts as $context)
                                        <option value="{{ $context->id }}" {{ old('context_id', $task->context_id) == $context->id ? 'selected' : '' }}>
                                            {{ $context->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('context_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Assigné à -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Assigné à</label>
                                <select name="user_id" id="user_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Non assigné</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Image actuelle -->
                            @if($task->image)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Image actuelle</label>
                                    <div class="mt-1">
                                        <img src="{{ Storage::url('tasks/' . $task->image) }}" 
                                             alt="Image actuelle" 
                                             class="max-w-xs h-32 object-cover rounded">
                                    </div>
                                </div>
                            @endif

                            <!-- Nouvelle image -->
                            <div class="md:col-span-2">
                                <label for="image" class="block text-sm font-medium text-gray-700">
                                    {{ $task->image ? 'Remplacer l\'image' : 'Ajouter une image' }}
                                </label>
                                <input type="file" name="image" id="image" accept="image/*"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       onchange="previewImage(this)">
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <!-- Prévisualisation de la nouvelle image -->
                                <div id="image-preview" class="mt-2 hidden">
                                    <img id="preview-img" src="" alt="Prévisualisation" class="max-w-xs h-32 object-cover rounded">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-2">
                            <a href="{{ route('tasks.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>