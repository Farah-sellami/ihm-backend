<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use App\Models\Scategorie;
use Illuminate\Http\Request;

class PosteController extends Controller
{
    // Show all postes
    public function index()
    {
        $postes = Poste::with('scategorie')->get();
        return response()->json($postes);
    }

    // Show a single poste
    public function show($id)
    {
        $poste = Poste::with('scategorie')->findOrFail($id);
        return response()->json($poste);
    }

    // Create a new poste
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:100',
            'photos' => 'required|string|max:255',
            'description' => 'required|string|max:100',
            'prixIniale' => 'required|numeric',
            'duree' => 'required|string|max:50',
            'estApprouvé' => 'required|boolean',
            'scategorieID' => 'required|exists:scategories,id',  // Ensure scategorieID exists in scategories table
        ]);

        $poste = Poste::create($validated);

        return response()->json([
            'message' => 'Poste created successfully',
            'poste' => $poste,
        ], 201);
    }

    // Update an existing poste
    public function update(Request $request, $id)
    {
        $poste = Poste::findOrFail($id);

        $validated = $request->validate([
            'titre' => 'sometimes|required|string|max:100',
            'photos' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:100',
            'prixIniale' => 'sometimes|required|numeric',
            'duree' => 'sometimes|required|string|max:50',
            'estApprouvé' => 'sometimes|required|boolean',
            'scategorieID' => 'sometimes|required|exists:scategories,id',
        ]);

        $poste->update($validated);

        return response()->json([
            'message' => 'Poste updated successfully',
            'poste' => $poste,
        ]);
    }

    // Delete a poste
    public function destroy($id)
    {
        $poste = Poste::findOrFail($id);
        $poste->delete();

        return response()->json([
            'message' => 'Poste deleted successfully',
        ]);
    }
}
