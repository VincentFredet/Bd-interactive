<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestion des utilisateurs
            </h2>
            <a href="{{ route('users.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nouvel utilisateur
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

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($users->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">Aucun utilisateur trouvé.</p>
                            <a href="{{ route('users.create') }}"
                               class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Créer le premier utilisateur
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tâches assignées
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date de création
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $user->name }}
                                                            @if($user->id === auth()->id())
                                                                <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                                    Vous
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $user->tasks_count }} tâche(s)
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('users.edit', $user) }}"
                                                       class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                                                        Modifier
                                                    </a>
                                                    @if($user->id !== auth()->id())
                                                        <form action="{{ route('users.destroy', $user) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
