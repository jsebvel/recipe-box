<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\RecipeViewed;
use App\Jobs\SendNewRecipeDigest;
use App\Contracts\RecipeSearcher;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, RecipeSearcher $search)
    {
        $query  = $request->input('q');

        $recipes = $query
            ? $search->find($query)
            : Recipe::with(['author', 'tags'])->latest()->get();

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'searchTerm' => $query
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
        /** @var \App\Models\User $user */
        $user = $request->user();

        $recipe = $user->recipes()->create($validate);


        SendNewRecipeDigest::dispatch($recipe);
        return redirect()->route('recipes.show', $recipe);
    }

    public function storeDraft(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'prep_minutes' => 'nullable|integer',
            'is_draft' => 'boolean'
        ]);

        $draft = $request->user()->recipes()->updateOrCreate(
            ['is_draft' => true, 'author_id' => $request->user()->id],
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

        event(new RecipeViewed($recipe));

        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|',
            'prep_minutes' => 'required|integer|between:1,600',
        ]);

        $recipe->update($validated);
        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Receta actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request, RecipeSearcher $search)
    {
        $results = $search->find($request->query('q', ''));
        return response()->json($results);
    }
}
