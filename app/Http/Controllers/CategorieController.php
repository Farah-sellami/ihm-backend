<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
            $request->validate([
                'titre' => 'required|string|unique:categories,titre',
                'description' => 'nullable|string',
                'image' => 'nullable|url', // ✅ on attend une URL ici
            ]);

            $data = $request->only(['titre', 'description', 'image']); // ✅ on récupère aussi image ici

            $categorie = Categorie::create($data);

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
        $request->validate([
            'titre' => 'required|string|unique:categories,titre,' . $categorie->id,
            'description' => 'nullable|string',
            'image' => 'nullable|url', // ✅ on valide l'URL ici aussi
        ]);


        $data = $request->only(['titre', 'description']);

        if ($request->hasFile('image')) {
            // (Optionnel) Supprimer l’ancienne image de Cloudinary si tu stockes l’ID public
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            $data['image'] = $uploadedFileUrl;
        }

        $categorie->update($data);

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
    public function getScategories($id)
    {
        try {
            // Find the category by ID
            $categorie = Categorie::findOrFail($id);

            // Access the scategories using the defined relationship
            $scategories = $categorie->scategories;

            return response()->json($scategories, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch scategories', 'message' => $e->getMessage()], 500);
        }
    }

}
