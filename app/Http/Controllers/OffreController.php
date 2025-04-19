<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    // Display all offers
    public function index()
    {
        $offres = Offre::all();
        return response()->json($offres);
    }

    // Display a specific offer
    public function show($id)
    {
        $offre = Offre::findOrFail($id);
        return response()->json($offre);
    }

    // Create a new offer
    public function store(Request $request)
{
    $validated = $request->validate([
        'montant' => 'required|numeric|min:0.01',
        'poste_id' => 'required|exists:postes,id',
        'user_id' => 'required|exists:users,id' // Validate user exists
    ]);

    // Create the offer with current timestamp
    $offre = Offre::create([
        'montant' => $validated['montant'],
        'dateEnchere' => now(),
        'poste_id' => $validated['poste_id'],
        'user_id' => $validated['user_id']
    ]);

    // Return the created offer and all offers for this post
    $offres = Offre::where('poste_id', $validated['poste_id'])
                  ->orderBy('created_at', 'desc')
                  ->get();

    return response()->json([
        'message' => 'Offer created successfully',
        'offre' => $offre,
        'offres' => $offres,
    ], 201);
}

    // Get offers by post
    public function getOffresByPoste($posteId)
    {
        $offres = Offre::where('poste_id', $posteId)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json($offres);
    }

    // Update an offer
    public function update(Request $request, $id)
    {
        $offre = Offre::findOrFail($id);

        $validated = $request->validate([
            'montant' => 'sometimes|required|numeric|min:0.01',
            // Don't allow updating poste_id or dateEnchere after creation
        ]);

        $offre->update($validated);

        return response()->json([
            'message' => 'Offer updated successfully',
            'offre' => $offre,
        ]);
    }

    // Delete an offer
    public function destroy($id)
    {
        $offre = Offre::findOrFail($id);
        $offre->delete();

        return response()->json([
            'message' => 'Offer deleted successfully',
        ]);
    }
}
