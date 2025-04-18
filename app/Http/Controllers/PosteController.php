<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use App\Models\Scategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PosteController extends Controller
{
    // Show all postes
//     public function index(Request $request)
// {
//     try {
//         $scategorieID = $request->input('scategorie_id');

//         if (!$scategorieID) {
//             return response()->json(['error' => 'scategorie_id is required'], 400);
//         }

//         $postes = Poste::with(['scategorie', 'user'])->filterByScategorie($scategorieID)->get();

//         return response()->json($postes);
//     } catch (\Exception $e) {
//         // Log the error for debugging
//         Log::error('Error fetching postes: ' . $e->getMessage(), [
//             'trace' => $e->getTraceAsString(),
//         ]);

//         // Return a JSON response with the error message
//         return response()->json([
//             'error' => 'An error occurred while fetching postes.',
//             'message' => $e->getMessage(),
//         ], 500);
//     }
// }

// Show all postes with optional filters
public function index(Request $request)
{
    try {
        $scategorieID = $request->input('scategorie_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        if (!$scategorieID) {
            return response()->json(['error' => 'scategorie_id is required'], 400);
        }

        $query = Poste::with(['scategorie', 'user']);

        // Filtrer par scategorie
        if (method_exists(Poste::class, 'scopeFilterByScategorie')) {
            $query->filterByScategorie($scategorieID);
        } else {
            $query->where('scategorieID', $scategorieID); // Assurez-vous que le nom de la colonne est correct
        }

        // Filtrer par plage de prix si disponible
        if ($minPrice && $maxPrice) {
            $query->whereBetween('prixIniale', [$minPrice, $maxPrice]);
        }

        $postes = $query->get();

        return response()->json($postes);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error fetching postes: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'An error occurred while fetching postes.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function index2()
    {
        $postes = Poste::all();
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
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:100',
                'photos' => 'required|array|max:4', // Validation pour accepter un tableau avec un maximum de 4 fichiers
                'photos.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048', // Chaque fichier doit être une image
                'description' => 'required|string|max:100',
                'prixIniale' => 'required|numeric',
                'duree' => 'required|string|max:50',
                'estApprouvé' => 'required|boolean',
                'scategorieID' => 'required|exists:scategories,id',
            ]);

            // Traiter l'upload des photos
            $photosUrls = [];
            foreach ($request->file('photos') as $photo) {
                $uploadedFileUrl = Cloudinary::upload($photo->getRealPath())->getSecurePath();
                $photosUrls[] = $uploadedFileUrl; // Ajouter chaque URL au tableau
            }

            // Créer le poste avec les URLs des photos
            $validated['photos'] = $photosUrls;
            $poste = Poste::create($validated);

            return response()->json([
                'message' => 'Poste created successfully',
                'poste' => $poste,
            ], 201);

        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Error creating poste: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while creating the poste.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // Update an existing poste
    public function update(Request $request, $id)
    {
        try {
            $poste = Poste::findOrFail($id);

            $validated = $request->validate([
                'titre' => 'sometimes|required|string|max:100',
                'photos' => 'sometimes|array|max:4', // Validation pour un tableau de photos
                'photos.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048', // Chaque fichier doit être une image
                'description' => 'sometimes|required|string|max:100',
                'prixIniale' => 'sometimes|required|numeric',
                'duree' => 'sometimes|required|string|max:50',
                'estApprouvé' => 'sometimes|required|boolean',
                'scategorieID' => 'sometimes|required|exists:scategories,id',
            ]);

            // Si de nouvelles photos sont envoyées, les uploader sur Cloudinary
            if ($request->has('photos')) {
                $photosUrls = [];
                foreach ($request->file('photos') as $photo) {
                    $uploadedFileUrl = Cloudinary::upload($photo->getRealPath())->getSecurePath();
                    $photosUrls[] = $uploadedFileUrl;
                }

                // Mettre à jour le champ photos
                $validated['photos'] = $photosUrls;
            }

            // Mettre à jour le poste
            $poste->update($validated);

            return response()->json([
                'message' => 'Poste updated successfully',
                'poste' => $poste,
            ]);
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Error updating poste: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while updating the poste.',
                'message' => $e->getMessage(),
            ], 500);
        }
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

    public function approvePoste($id)
{
    try {
        $poste = Poste::findOrFail($id);

        // Changer l'état de 'estApprouvé'
        $poste->estApprouvé = true;
        $poste->save();

        return response()->json([
            'message' => 'Poste approuvé avec succès',
            'poste' => $poste,
        ]);
    } catch (\Exception $e) {
        // Log l'erreur pour le débogage
        Log::error('Error approving poste: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'An error occurred while approving the poste.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function disapprovePoste($id)
{
    try {
        $poste = Poste::findOrFail($id);

        // Changer l'état de 'estApprouvé' à false
        $poste->estApprouvé = false;
        $poste->save();

        return response()->json([
            'message' => 'Poste désapprouvé avec succès',
            'poste' => $poste,
        ]);
    } catch (\Exception $e) {
        // Log l'erreur pour le débogage
        Log::error('Error disapproving poste: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'An error occurred while disapproving the poste.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function getPostesfiltred(Request $request)
{
    $filter = $request->query('filter'); // Filtre des postes
    $page = $request->query('page', 1);  // Par défaut, la page est 1

    $query = Poste::query();

    if ($filter) {
        $query->where('est_approuvé', $filter === 'approved' ? true : false);
    }

    $postes = $query->paginate(10, ['*'], 'page', $page);

    return response()->json([
        'postes' => $postes->items(),
        'totalPages' => $postes->lastPage(),
    ]);
}


}
