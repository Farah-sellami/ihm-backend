<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    // Register Function
    public function register(Request $request)
    {
        $request->validate([
            'CIN' => 'required|string|unique:users',
            'motDePasse' => 'required|min:6',
            'nom' => 'required|string',
            'prenom' => 'required|string', // Updated to 'prenom'
            'dateNaissance' => 'required|date',
            'ville' => 'required|string',
            'photoProfil' => 'nullable|file|image',
            //'role' => 'required|integer|in:0,1', // 0 = admin, 1 = normal user
            //'type' => 'nullable|in:A,V', // Valid values: 'A' or 'V'
        ]);
       // Définir une valeur par défaut pour l'URL de la photo
            $uploadedFileUrl = null;
              // Si une photo de profil est fournie
        if ($request->hasFile('photoProfil')) {
            try {
                $uploadedFileUrl = Cloudinary::upload($request->file('photoProfil')->getRealPath())->getSecurePath();
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'upload de la photo de profil',
                 [
                    'exception' => $e->getMessage(),
                    'stack' => $e->getTraceAsString(),
                ]);
                return response()->json(['error' => 'Erreur lors de l\'upload de la photo de profil', 'details' => $e->getMessage()], 500);
            }
        }

        $user = User::create([
            'CIN' => $request->CIN,
            'nom' => $request->nom,
            'prenom' => $request->prenom, // Updated to 'prenom'
            'dateNaissance' => $request->dateNaissance,
            'ville' => $request->ville,
            'photoProfil' =>  $uploadedFileUrl,
            'motDePasse' => Hash::make($request->motDePasse),
            'role' => 1, // utilisateur normal par défaut
            //'type' => $request->type, // peut être null
        ]);

        // If the user is an admin, insert into the `admins` table
        // if ($user->role == 0) {
        //     Admin::create([
        //         'user_id' => $user->id,
        //         'nom' => $user->nom,
        //         'prenom' => $user->prenom, // Updated to 'prenom'
        //     ]);
        // }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Login Function
    public function login(Request $request)
    {
        $request->validate([
            'CIN' => 'required|string',
            'motDePasse' => 'required|string',
        ]);

        $user = User::where('CIN', $request->CIN)->first();

        if (!$user || !Hash::check($request->motDePasse, $user->motDePasse)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
         // Vérifier si l'utilisateur est bloqué
         if ($user->status === 'blocked') {
            return response()->json(['message' => 'Votre compte est bloqué. Veuillez contacter l\'administrateur.'], 403);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
