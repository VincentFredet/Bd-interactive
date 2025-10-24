<?php

namespace App\Http\Controllers;

use App\Models\Context;
use Illuminate\Http\Request;

class ContextController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contexts = Context::withCount('tasks')->get();
        return view('contexts.index', compact('contexts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $colors = Context::COLORS;
        return view('contexts.create', compact('colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contexts,name',
            'color' => 'required|string|in:' . implode(',', array_keys(Context::COLORS)),
        ]);

        Context::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Contexte créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Context $context)
    {
        $context->load('tasks.user');
        return view('contexts.show', compact('context'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Context $context)
    {
        $colors = Context::COLORS;
        return view('contexts.edit', compact('context', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Context $context)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contexts,name,' . $context->id,
            'color' => 'required|string|in:' . implode(',', array_keys(Context::COLORS)),
        ]);

        $context->update($validated);

        return redirect()->route('contexts.index')->with('success', 'Contexte mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Context $context)
    {
        $context->delete();

        return redirect()->route('contexts.index')->with('success', 'Contexte supprimé avec succès!');
    }
}
