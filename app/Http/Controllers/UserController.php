<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    public function index()
{
    // Filter users where the role is 1 (normal user)
    $users = User::where('role', 1)->get();

    return response()->json($users);
}


    // Méthode pour afficher un utilisateur spécifique
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json($user);
    }

    // Méthode pour créer un utilisateur
    // public function store(Request $request)
    // {
    //     // Validation des données
    //     $validator = Validator::make($request->all(), [
    //         'CIN' => 'required|unique:users,CIN',
    //         'nom' => 'required|string',
    //         'prénom' => 'required|string',
    //         'dateNaissance' => 'required|date',
    //         'ville' => 'required|string',
    //         'photoProfil' => 'nullable|image',
    //         'motDePasse' => 'required|string|min:6',
    //         'role' => 'required|string',
    //         'type' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }

    //     // Créer un nouvel utilisateur
    //     $user = new User([
    //         'CIN' => $request->CIN,
    //         'nom' => $request->nom,
    //         'prénom' => $request->prénom,
    //         'dateNaissance' => $request->dateNaissance,
    //         'ville' => $request->ville,
    //         'photoProfil' => $request->photoProfil,
    //         'motDePasse' => bcrypt($request->motDePasse),
    //         'role' => $request->role,
    //         'type' => $request->type,
    //     ]);

    //     $user->save();

    //     return response()->json($user, 201);
    // }

    // Méthode pour mettre à jour un utilisateur
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'CIN' => 'required|unique:users,CIN,' . $id,
            'nom' => 'required|string',
            'prénom' => 'required|string',
            'dateNaissance' => 'required|date',
            'ville' => 'required|string',
            'photoProfil' => 'nullable|image',
            'motDePasse' => 'nullable|string|min:6',
            'role' => 'required|string',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Mettre à jour les informations de l'utilisateur
        $user->CIN = $request->CIN;
        $user->nom = $request->nom;
        $user->prénom = $request->prénom;
        $user->dateNaissance = $request->dateNaissance;
        $user->ville = $request->ville;
        $user->photoProfil = $request->photoProfil;
        $user->role = $request->role;
        $user->type = $request->type;

        // Si un mot de passe est fourni, le mettre à jour
        if ($request->has('motDePasse')) {
            $user->motDePasse = bcrypt($request->motDePasse);
        }

        $user->save();

        return response()->json($user);
    }

    // Méthode pour supprimer un utilisateur
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

        public function blockUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Bloque l'utilisateur
        $user->is_blocked = true;
        $user->save();

        return response()->json(['message' => 'Utilisateur bloqué avec succès']);
    }

    public function unblockUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Débloque l'utilisateur
        $user->is_blocked = false;
        $user->save();

        return response()->json(['message' => 'Utilisateur débloqué avec succès']);
    }


}
