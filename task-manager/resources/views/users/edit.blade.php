<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier l'utilisateur : {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom complet *</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Ex: Jean Dupont">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail *</label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="jean.dupont@example.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm text-blue-800 mb-2">
                                <strong>Changer le mot de passe (optionnel)</strong>
                            </p>
                            <p class="text-xs text-blue-600">
                                Laissez ces champs vides si vous ne souhaitez pas changer le mot de passe.
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                            <input type="password" name="password" id="password"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Minimum 8 caractères (optionnel)">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Répétez le mot de passe (optionnel)">
                        </div>

                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('users.index') }}"
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
