<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Recipes/Index', [
            'recipes'=> Recipe::with(['author', 'tags'])->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Recipes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'prep_minutes' => 'required|integer|between:1,600'
        ]);

        $recipe = Auth::user()->recipes->create($validate);

        return redirect()->route('recipes.show', $recipe);

    }

    public function storeDraft(Request $request)
    {
        $validated = $request->validate([
            'title'=> 'nullable|string|max:255',
            'body'=> 'nullable|string',
            'prep_minutes'=>'nullable|integer',
            'is_draft' => 'boolean'
        ]);

        $draft = $request->user()->recipes()->updateOrCreate(
            ['is_draft' => true, 'author_id'=> $request->user()->id],
            array_merge($validated, ['is_draft' => true])
        );

        return redirect()->back()->with('success', 'Borrador guardado');
    }
    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load(['author', 'tags']);

        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
