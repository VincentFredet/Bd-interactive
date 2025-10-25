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

                            <!-- Catégories -->
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Catégories</label>
                                    <button type="button" onclick="openCategoryModal()"
                                            class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Nouvelle catégorie
                                    </button>
                                </div>
                                <select name="categories[]" id="categories" multiple
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        size="5">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ in_array($category->id, old('categories', $task->categories->pluck('id')->toArray())) ? 'selected' : '' }}
                                                data-color="{{ $category->color }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs catégories</p>
                                @error('categories')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Semaine -->
                            <div>
                                <label for="week_date" class="block text-sm font-medium text-gray-700">Semaine</label>
                                <input type="date" name="week_date" id="week_date"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('week_date', $task->week_date ? $task->week_date->format('Y-m-d') : '') }}">
                                <p class="mt-1 text-xs text-gray-500">La tâche sera automatiquement assignée au début de cette semaine</p>
                                @error('week_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date d'échéance -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Date d'échéance</label>
                                <input type="date" name="due_date" id="due_date"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                                <p class="mt-1 text-xs text-gray-500">Date à laquelle la tâche doit être terminée</p>
                                @error('due_date')
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

            <!-- Galerie d'images multiples -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📸 Galerie d'images</h3>

                    <!-- Images existantes -->
                    @if($task->images && $task->images->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            @foreach($task->images as $image)
                                <div class="relative group">
                                    <img src="{{ $image->url }}" alt="{{ $image->original_name }}"
                                         class="w-full h-32 object-cover rounded-lg shadow">
                                    <button type="button"
                                            onclick="deleteImage({{ $image->id }})"
                                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                            title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="text-xs text-gray-500 mt-1 truncate" title="{{ $image->original_name }}">
                                        {{ $image->original_name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-4">Aucune image dans la galerie</p>
                    @endif

                    <!-- Formulaire d'upload multiple -->
                    <form action="{{ route('task-images.store', $task) }}" method="POST" enctype="multipart/form-data" id="images-upload-form">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <input type="file" name="images[]" id="images" multiple accept="image/*"
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
                                ➕ Ajouter
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Vous pouvez sélectionner plusieurs images à la fois (max 5 Mo chacune)</p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteImage(imageId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
                return;
            }

            fetch(`/task-images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la suppression');
            });
        }
    </script>

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

        // Gestion du modal de création rapide de catégorie
        function openCategoryModal() {
            document.getElementById('category-modal').classList.remove('hidden');
        }

        function closeCategoryModal() {
            document.getElementById('category-modal').classList.add('hidden');
            document.getElementById('quick-category-form').reset();
            document.getElementById('category-error').classList.add('hidden');
        }

        function createQuickCategory() {
            const form = document.getElementById('quick-category-form');
            const formData = new FormData(form);
            const errorDiv = document.getElementById('category-error');

            fetch('{{ route('categories.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ajouter la nouvelle catégorie au select
                    const select = document.getElementById('categories');
                    const option = new Option(data.category.name, data.category.id, true, true);
                    option.setAttribute('data-color', data.category.color);
                    select.add(option);

                    // Fermer le modal
                    closeCategoryModal();
                } else {
                    errorDiv.textContent = 'Erreur lors de la création de la catégorie';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                errorDiv.textContent = 'Erreur lors de la création de la catégorie';
                errorDiv.classList.remove('hidden');
            });
        }
    </script>

    <!-- Modal de création rapide de catégorie -->
    <div id="category-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Créer une nouvelle catégorie</h3>
                <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="quick-category-form" onsubmit="event.preventDefault(); createQuickCategory();">
                @csrf
                <div class="mb-4">
                    <label for="quick-name" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="name" id="quick-name" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: Design, Développement...">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur *</label>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach(\App\Models\Category::COLORS as $colorKey => $colorName)
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="{{ $colorKey }}"
                                       {{ $colorKey === 'blue' ? 'checked' : '' }}
                                       class="sr-only peer" required>
                                <div class="w-10 h-10 rounded-full bg-{{ $colorKey }}-500 border-2 border-gray-200
                                            peer-checked:border-{{ $colorKey }}-700 peer-checked:ring-2 peer-checked:ring-{{ $colorKey }}-200
                                            hover:scale-110 transition-transform"
                                     title="{{ $colorName }}">
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div id="category-error" class="hidden mb-4 text-sm text-red-600"></div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeCategoryModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>