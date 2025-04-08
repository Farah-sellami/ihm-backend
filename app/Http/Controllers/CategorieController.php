<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Categorie::all();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch categories', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'titre' => 'required|string|unique:categories,titre',
                'description' => 'nullable|string',
            ]);

            // Create category
            $categorie = new Categorie([
                'titre' => $request->input('titre'),
                'description' => $request->input('description'),
            ]);
            $categorie->save();

            return response()->json($categorie, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create category', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        try {
            return response()->json($categorie, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch category', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categorie $categorie)
    {
        try {
            // Validate input
            $request->validate([
                'titre' => 'required|string|unique:categories,titre,' . $categorie->id,
                'description' => 'nullable|string',
            ]);

            // Update category
            $categorie->update($request->all());

            return response()->json($categorie, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update category', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorie $categorie)
    {
        try {
            $categorie->delete();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete category', 'message' => $e->getMessage()], 500);
        }
    }

    //Pour récupérer les sous-catégories (scategories) associées à une catégorie
    public function getSousCategories($id)
{
    $categorie = Categorie::with('scategories')->find($id);

    if (!$categorie) {
        return response()->json(['message' => 'Catégorie non trouvée'], 404);
    }

    return response()->json([
        'categorie' => $categorie->titre,
        'sous_categories' => $categorie->scategories
    ]);
}
}
