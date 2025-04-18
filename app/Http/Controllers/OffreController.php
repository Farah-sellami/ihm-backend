<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    // Afficher toutes les offres
    public function index()
    {
        $offres = Offre::all();
        return response()->json($offres);
    }

    // Afficher une offre spécifique
    public function show($id)
    {
        $offre = Offre::findOrFail($id);
        return response()->json($offre);
    }

    // Créer une nouvelle offre
    public function store(Request $request)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric',
            'dateEnchere' => 'required|date',
            'poste_id' => 'required|exists:postes,id', // Poste associé doit exister
        ]);

        $offre = Offre::create($validated);

        return response()->json([
            'message' => 'Offre créée avec succès',
            'offre' => $offre,
        ], 201);
    }

    // Mettre à jour une offre
    public function update(Request $request, $id)
    {
        $offre = Offre::findOrFail($id);

        $validated = $request->validate([
            'montant' => 'sometimes|required|numeric',
            'dateEnchere' => 'sometimes|required|date',
            'poste_id' => 'sometimes|required|exists:postes,id',
        ]);

        $offre->update($validated);

        return response()->json([
            'message' => 'Offre mise à jour avec succès',
            'offre' => $offre,
        ]);
    }

    // Supprimer une offre
    public function destroy($id)
    {
        $offre = Offre::findOrFail($id);
        $offre->delete();

        return response()->json([
            'message' => 'Offre supprimée avec succès',
        ]);
    }

    public function getOffresByPoste($posteId)
    {
        // Récupérer toutes les offres associées au poste donné
        $offres = Offre::where('poste_id', $posteId)->get();

       

        return response()->json($offres);
    }
}
