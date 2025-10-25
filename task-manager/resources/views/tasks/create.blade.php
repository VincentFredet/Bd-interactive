<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une nouvelle tâche') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Titre -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Titre *</label>
                                <input type="text" name="title" id="title" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('title') }}">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                                <select name="status" id="status"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="todo" {{ old('status') === 'todo' ? 'selected' : '' }}>À faire</option>
                                    <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                    <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Terminé</option>
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
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Faible</option>
                                    <option value="medium" {{ old('priority') === 'medium' || !old('priority') ? 'selected' : '' }}>Moyenne</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Élevée</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
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
                                        <option value="{{ $context->id }}" {{ old('context_id') == $context->id ? 'selected' : '' }}>
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
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                                {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}
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
                                       value="{{ old('week_date', request('week', \App\Models\Task::getWeekStart()->format('Y-m-d'))) }}">
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
                                       value="{{ old('due_date', request('date', \Carbon\Carbon::today()->format('Y-m-d'))) }}">
                                <p class="mt-1 text-xs text-gray-500">Date à laquelle la tâche doit être terminée</p>
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Image -->
                            <div class="md:col-span-2">
                                <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                                <input type="file" name="image" id="image" accept="image/*"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       onchange="previewImage(this)">
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Prévisualisation de l'image -->
                                <div id="image-preview" class="mt-2 hidden">
                                    <img id="preview-img" src="" alt="Prévisualisation" class="max-w-xs h-32 object-cover rounded">
                                </div>
                            </div>

                            <!-- Récurrence -->
                            <div class="md:col-span-2 border-t pt-4 mt-4">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                                           {{ old('is_recurring') ? 'checked' : '' }}
                                           onchange="toggleRecurrence()"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_recurring" class="ml-2 block text-sm font-medium text-gray-700">
                                        🔄 Tâche récurrente
                                    </label>
                                </div>

                                <div id="recurrence-options" class="hidden space-y-4 pl-6 border-l-2 border-indigo-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Type de récurrence -->
                                        <div>
                                            <label for="recurrence_type" class="block text-sm font-medium text-gray-700">Fréquence</label>
                                            <select name="recurrence_type" id="recurrence_type"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="daily" {{ old('recurrence_type') === 'daily' ? 'selected' : '' }}>Quotidienne</option>
                                                <option value="weekly" {{ old('recurrence_type', 'weekly') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                                <option value="monthly" {{ old('recurrence_type') === 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                                                <option value="yearly" {{ old('recurrence_type') === 'yearly' ? 'selected' : '' }}>Annuelle</option>
                                            </select>
                                        </div>

                                        <!-- Intervalle -->
                                        <div>
                                            <label for="recurrence_interval" class="block text-sm font-medium text-gray-700">Tous les</label>
                                            <div class="flex items-center space-x-2">
                                                <input type="number" name="recurrence_interval" id="recurrence_interval" min="1" value="{{ old('recurrence_interval', 1) }}"
                                                       class="mt-1 block w-20 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <span class="text-sm text-gray-600" id="recurrence-interval-label">semaine(s)</span>
                                            </div>
                                        </div>

                                        <!-- Date de fin (optionnelle) -->
                                        <div class="md:col-span-2">
                                            <label for="recurrence_end_date" class="block text-sm font-medium text-gray-700">Date de fin (optionnelle)</label>
                                            <input type="date" name="recurrence_end_date" id="recurrence_end_date"
                                                   value="{{ old('recurrence_end_date') }}"
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <p class="mt-1 text-xs text-gray-500">Laissez vide pour une récurrence illimitée</p>
                                        </div>
                                    </div>

                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                        <p class="text-sm text-blue-800">
                                            <strong>ℹ️ Info :</strong> Les tâches récurrentes génèrent automatiquement de nouvelles instances selon le modèle défini.
                                            La tâche originale sert de template et ne disparaît pas.
                                        </p>
                                    </div>
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
                                Créer la tâche
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

        // Gestion de l'affichage des options de récurrence
        function toggleRecurrence() {
            const checkbox = document.getElementById('is_recurring');
            const options = document.getElementById('recurrence-options');

            if (checkbox.checked) {
                options.classList.remove('hidden');
            } else {
                options.classList.add('hidden');
            }
        }

        // Mettre à jour le label de l'intervalle selon le type
        document.getElementById('recurrence_type')?.addEventListener('change', function() {
            const label = document.getElementById('recurrence-interval-label');
            const labels = {
                'daily': 'jour(s)',
                'weekly': 'semaine(s)',
                'monthly': 'mois',
                'yearly': 'an(s)'
            };
            label.textContent = labels[this.value] || 'unité(s)';
        });

        // Afficher les options si la checkbox est cochée au chargement (old input)
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('is_recurring').checked) {
                document.getElementById('recurrence-options').classList.remove('hidden');
            }
        });

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

            // Cacher les erreurs précédentes
            errorDiv.classList.add('hidden');

            fetch('{{ route('categories.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    // Gérer les erreurs de validation (422)
                    return response.json().then(err => {
                        throw err;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Ajouter la nouvelle catégorie au select
                    const select = document.getElementById('categories');
                    const option = new Option(data.category.name, data.category.id, true, true);
                    option.setAttribute('data-color', data.category.color);
                    select.add(option);

                    // Réinitialiser le formulaire
                    form.reset();
                    document.getElementById('quick-color').value = 'blue';
                    document.getElementById('quick-custom-color').classList.add('hidden');

                    // Fermer le modal
                    closeCategoryModal();
                } else {
                    errorDiv.textContent = 'Erreur lors de la création de la catégorie';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Afficher les erreurs de validation
                if (error.errors) {
                    const messages = Object.values(error.errors).flat();
                    errorDiv.textContent = messages.join(', ');
                } else if (error.message) {
                    errorDiv.textContent = error.message;
                } else {
                    errorDiv.textContent = 'Erreur lors de la création de la catégorie';
                }
                errorDiv.classList.remove('hidden');
            });
        }
    </script>

    <!-- Modal de création rapide de catégorie -->
    <div id="category-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4 px-2 pt-2">
                <h3 class="text-lg font-medium text-gray-900">Créer une nouvelle catégorie</h3>
                <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="quick-category-form" onsubmit="event.preventDefault(); createQuickCategory();" class="px-2 pb-2">
                @csrf
                <div class="mb-4">
                    <label for="quick-name" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="name" id="quick-name" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: Design, Développement...">
                </div>

                <div class="mb-4">
                    <label for="quick-context" class="block text-sm font-medium text-gray-700 mb-2">Contexte *</label>
                    <select name="context_id" id="quick-context" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner un contexte</option>
                        @foreach($contexts as $context)
                            <option value="{{ $context->id }}">{{ $context->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur *</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        @php
                            $colorHexValues = [
                                'gray' => '#6B7280',
                                'blue' => '#3B82F6',
                                'green' => '#10B981',
                                'yellow' => '#F59E0B',
                                'red' => '#EF4444',
                                'purple' => '#8B5CF6',
                                'pink' => '#EC4899',
                                'indigo' => '#6366F1',
                                'teal' => '#14B8A6',
                                'orange' => '#F97316',
                            ];
                        @endphp
                        @foreach(\App\Models\Category::COLORS as $colorKey => $colorName)
                            <label class="cursor-pointer" style="position: relative;">
                                <input type="radio" name="color_type" value="{{ $colorKey }}"
                                       {{ $colorKey === 'blue' ? 'checked' : '' }}
                                       onclick="document.getElementById('quick-color').value='{{ $colorKey }}'; document.getElementById('quick-custom-color').classList.add('hidden');"
                                       style="position: absolute; width: 1px; height: 1px; opacity: 0;" required>
                                <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #e5e7eb; background-color: {{ $colorHexValues[$colorKey] ?? '#6B7280' }}; transition: all 0.2s; cursor: pointer;"
                                     onmouseover="this.style.transform='scale(1.1)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     title="{{ $colorName }}">
                                </div>
                            </label>
                        @endforeach
                        <label class="cursor-pointer" style="position: relative;">
                            <input type="radio" name="color_type" value="custom"
                                   onclick="document.getElementById('quick-custom-color').classList.remove('hidden'); document.getElementById('quick-hex-input').focus();"
                                   style="position: absolute; width: 1px; height: 1px; opacity: 0;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #e5e7eb; background: linear-gradient(135deg, #ef4444 0%, #eab308 50%, #3b82f6 100%); transition: all 0.2s; cursor: pointer;"
                                 onmouseover="this.style.transform='scale(1.1)'"
                                 onmouseout="this.style.transform='scale(1)'"
                                 title="Personnalisée">
                            </div>
                        </label>
                    </div>

                    <div id="quick-custom-color" class="hidden flex items-center gap-2">
                        <label for="quick-hex-input" class="text-xs text-gray-600">Hex:</label>
                        <input type="text" id="quick-hex-input"
                               placeholder="#FF5733"
                               oninput="document.getElementById('quick-color').value = this.value;"
                               class="border-gray-300 rounded-md shadow-sm text-xs focus:ring-indigo-500 focus:border-indigo-500 flex-1">
                        <input type="color"
                               onchange="document.getElementById('quick-hex-input').value = this.value; document.getElementById('quick-color').value = this.value;"
                               class="w-10 h-10 cursor-pointer rounded border-2 border-gray-300">
                    </div>

                    <input type="hidden" name="color" id="quick-color" value="blue">
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