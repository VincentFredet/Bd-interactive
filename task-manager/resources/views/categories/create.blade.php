<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une nouvelle catégorie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom de la catégorie *</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   value="{{ old('name') }}"
                                   placeholder="Ex: Design, Développement, Marketing...">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Couleur de la catégorie *</label>
                            <div class="grid grid-cols-5 gap-3">
                                @foreach($colors as $colorKey => $colorName)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="color_type" value="{{ $colorKey }}"
                                               {{ old('color_type', 'blue') == $colorKey ? 'checked' : '' }}
                                               onclick="document.getElementById('color').value='{{ $colorKey }}'; document.getElementById('custom-color-input').classList.add('hidden');"
                                               class="sr-only peer" required>
                                        <div class="flex flex-col items-center p-3 rounded-lg border-2 border-gray-200
                                                    peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200
                                                    hover:border-gray-300 transition-all">
                                            <div class="w-12 h-12 rounded-full mb-2" style="background-color: {{ $colorHexMap[$colorKey] }};"></div>
                                            <span class="text-xs text-gray-600 text-center">{{ $colorName }}</span>
                                        </div>
                                    </label>
                                @endforeach

                                <!-- Option couleur personnalisée -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="color_type" value="custom"
                                           {{ old('color_type') == 'custom' ? 'checked' : '' }}
                                           onclick="document.getElementById('custom-color-input').classList.remove('hidden'); document.getElementById('custom-hex').focus();"
                                           class="sr-only peer">
                                    <div class="flex flex-col items-center p-3 rounded-lg border-2 border-gray-200
                                                peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200
                                                hover:border-gray-300 transition-all">
                                        <div class="w-12 h-12 rounded-full mb-2 bg-gradient-to-br from-red-500 via-yellow-500 to-blue-500"></div>
                                        <span class="text-xs text-gray-600 text-center">Personnalisée</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Input couleur personnalisée -->
                            <div id="custom-color-input" class="{{ old('color_type') == 'custom' ? '' : 'hidden' }} mt-4 flex items-center gap-3">
                                <label for="custom-hex" class="text-sm font-medium text-gray-700">Code couleur :</label>
                                <input type="text" id="custom-hex"
                                       placeholder="#FF5733"
                                       value="{{ old('color_type') == 'custom' ? old('color') : '' }}"
                                       oninput="document.getElementById('color').value = this.value; document.getElementById('color-preview').style.backgroundColor = this.value;"
                                       class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-32">
                                <div id="color-preview" class="w-10 h-10 rounded-full border-2 border-gray-300"
                                     style="background-color: {{ old('color_type') == 'custom' ? old('color') : '#6B7280' }};"></div>
                                <input type="color"
                                       value="{{ old('color_type') == 'custom' ? old('color') : '#6B7280' }}"
                                       onchange="document.getElementById('custom-hex').value = this.value; document.getElementById('color').value = this.value; document.getElementById('color-preview').style.backgroundColor = this.value;"
                                       class="w-10 h-10 cursor-pointer">
                            </div>

                            <input type="hidden" name="color" id="color" value="{{ old('color', 'blue') }}">

                            @error('color')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('categories.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Créer la catégorie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
