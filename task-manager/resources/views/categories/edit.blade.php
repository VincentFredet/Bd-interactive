<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier la catégorie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom de la catégorie *</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   value="{{ old('name', $category->name) }}"
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
                                        <input type="radio" name="color" value="{{ $colorKey }}"
                                               {{ old('color', $category->color) == $colorKey ? 'checked' : '' }}
                                               class="sr-only peer" required>
                                        <div class="flex flex-col items-center p-3 rounded-lg border-2 border-gray-200
                                                    peer-checked:border-{{ $colorKey }}-500 peer-checked:ring-2 peer-checked:ring-{{ $colorKey }}-200
                                                    hover:border-{{ $colorKey }}-300 transition-all">
                                            <div class="w-12 h-12 rounded-full bg-{{ $colorKey }}-500 mb-2"></div>
                                            <span class="text-xs text-gray-600 text-center">{{ $colorName }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
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
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
