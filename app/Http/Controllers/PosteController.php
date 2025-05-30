<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use App\Models\Scategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PosteController extends Controller
{
    // Show all postes with optional filters
       // Fonction principale pour afficher les postes avec filtres
       public function index(Request $request)
       {
           try {
               $scategorieID = $request->input('scategorie_id');
               $minPrice = $request->input('min_price');
               $maxPrice = $request->input('max_price');

               $query = Poste::with(['scategorie', 'user']);

               // Si aucune sous-catégorie n'est sélectionnée, on récupère tous les postes
               if (!$scategorieID) {
                   // Récupère tous les postes sans filtrer par sous-catégorie
                   $postes = Poste::all();
               } else {
                   // Si une sous-catégorie est sélectionnée, on filtre les postes
                   $query->where('scategorieID', $scategorieID);

                   // Si des plages de prix sont définies, on les applique
                   if ($minPrice && $maxPrice) {
                       $query->whereBetween('prixIniale', [$minPrice, $maxPrice]);
                   }

                   // Récupère les postes après application des filtres
                   $postes = $query->get();
               }

               return response()->json($postes);
           } catch (\Exception $e) {
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

    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'titre' => 'required|string|max:100',
            'photos' => 'required|array|max:4',
            'photos.*' => 'url',
            'description' => 'required|string|max:100',
            'prixIniale' => 'required|numeric',
            'endDate' => 'required|date',
            'estApprouvé' => 'required|boolean',
            'scategorieID' => 'required|exists:scategories,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $poste = Poste::create([
            'titre' => $validated['titre'],
            'photos' => $validated['photos'], // $casts handles conversion to JSON
            'description' => $validated['description'],
            'prixIniale' => $validated['prixIniale'],
            'endDate' => $validated['endDate'],
            'estApprouvé' => $validated['estApprouvé'],
            'scategorieID' => $validated['scategorieID'],
            'user_id' => $validated['user_id']
        ]);

        return response()->json([
            'message' => 'Poste created successfully',
            'poste' => $poste,
        ], 201);
    } catch (\Exception $e) {
        Log::error('Error creating poste: ' . $e->getMessage());
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
                'photos' => 'sometimes|array|max:4',
                'photos.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
                'description' => 'sometimes|required|string|max:100',
                'prixIniale' => 'sometimes|required|numeric',
                'endDate' => 'sometimes|required|date', // ✅ nouveau champ
                'estApprouvé' => 'sometimes|required|boolean',
                'scategorieID' => 'sometimes|required|exists:scategories,id',
            ]);

            if ($request->has('photos')) {
                $photosUrls = [];
                foreach ($request->file('photos') as $photo) {
                    $uploadedFileUrl = Cloudinary::upload($photo->getRealPath())->getSecurePath();
                    $photosUrls[] = $uploadedFileUrl;
                }
                $validated['photos'] = $photosUrls;
            }

            $poste->update($validated);

            return response()->json([
                'message' => 'Poste updated successfully',
                'poste' => $poste,
            ]);
        } catch (\Exception $e) {
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

    // Approve a poste
    public function approvePoste($id)
    {
        try {
            $poste = Poste::findOrFail($id);
            $poste->estApprouvé = true;
            $poste->save();

            return response()->json([
                'message' => 'Poste approuvé avec succès',
                'poste' => $poste,
            ]);
        } catch (\Exception $e) {
            Log::error('Error approving poste: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while approving the poste.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Disapprove a poste
    public function disapprovePoste($id)
    {
        try {
            $poste = Poste::findOrFail($id);
            $poste->estApprouvé = false;
            $poste->save();

            return response()->json([
                'message' => 'Poste désapprouvé avec succès',
                'poste' => $poste,
            ]);
        } catch (\Exception $e) {
            Log::error('Error disapproving poste: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while disapproving the poste.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Filtered postes
    public function getPostesfiltred(Request $request)
    {
        $filter = $request->query('filter');
        $page = $request->query('page', 1);

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
