<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\ColorHelper;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('tasks')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $colors = Category::COLORS;
        $colorHexMap = ColorHelper::getColorHexMap();
        return view('categories.create', compact('colors', 'colorHexMap'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'color' => 'required|string|in:' . implode(',', array_keys(Category::COLORS)),
        ]);

        $category = Category::create($validated);

        // Si c'est une requête AJAX (pour la création rapide)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Catégorie créée avec succès!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $colors = Category::COLORS;
        $colorHexMap = ColorHelper::getColorHexMap();
        return view('categories.edit', compact('category', 'colors', 'colorHexMap'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'color' => 'required|string|in:' . implode(',', array_keys(Category::COLORS)),
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès!');
    }
}
