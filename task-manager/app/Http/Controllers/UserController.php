<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::withCount('tasks')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('tasks');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the currently authenticated user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte depuis cette interface.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès!');
    }
}
