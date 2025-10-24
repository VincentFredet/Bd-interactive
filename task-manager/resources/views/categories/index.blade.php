<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des catégories') }}
            </h2>
            <a href="{{ route('categories.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nouvelle catégorie
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($categories->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">Aucune catégorie créée.</p>
                            <a href="{{ route('categories.create') }}"
                               class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Créer votre première catégorie
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($categories as $category)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-{{ $category->color }}-500"></div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded {{ $category->badge_class }}">
                                            {{ $category->tasks_count }} tâche(s)
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-end space-x-2 mt-3">
                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Modifier
                                        </a>
                                        <form action="{{ route('categories.destroy', $category) }}"
                                              method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
