<?php
namespace App\Observers;

use App\Models\Admin;
use App\Models\User;

class UserObserver
{
    public function created(User $utilisateur)
    {
        // Si le rôle est 0 (Admin), ajouter automatiquement à la table admins
        if ($utilisateur->role == 0) {
            Admin::create([
                'user_id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom
            ]);
        }
    }
}
