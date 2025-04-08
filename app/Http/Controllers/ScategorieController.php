<?php

namespace App\Http\Controllers;

use App\Models\Scategorie;
use Illuminate\Http\Request;

class ScategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $scategories = Scategorie::with('categorie')->get(); // Include related categories
            return response()->json($scategories, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subcategories', 'message' => $e->getMessage()], 500);
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
                'titre' => 'required|string|unique:scategories,titre',
                'categorieID' => 'required|exists:categories,id', // Ensure the category exists
            ]);

            // Create subcategory
            $scategorie = new Scategorie([
                'titre' => $request->input('titre'),
                'categorieID' => $request->input('categorieID'),
            ]);
            $scategorie->save();

            return response()->json($scategorie, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create subcategory', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Scategorie $scategorie)
    {
        try {
            $scategorie->load('categorie'); // Load related category
            return response()->json($scategorie, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subcategory', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scategorie $scategorie)
    {
        try {
            // Validate input
            $request->validate([
                'titre' => 'required|string|unique:scategories,titre,' . $scategorie->id,
                'categorieID' => 'required|exists:categories,id',
            ]);

            // Update subcategory
            $scategorie->update($request->all());

            return response()->json($scategorie, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update subcategory', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scategorie $scategorie)
    {
        try {
            $scategorie->delete();
            return response()->json(['message' => 'Subcategory deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete subcategory', 'message' => $e->getMessage()], 500);
        }
    }

        /**
     * Get subcategories by category ID.
     */
    public function getByCategory($categorieID)
    {
        try {
            $subcategories = Scategorie::where('categorieID', $categorieID)->get();
            return response()->json([
                'sous_categories' => $subcategories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch subcategories by category',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
