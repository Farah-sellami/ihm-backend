<?php

namespace App\Providers;

use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
   // Observer pour les événements sur le modèle User
      User::observe(UserObserver::class);
       // Créer un utilisateur admin si aucun utilisateur avec le rôle 0 n'existe
      if (!User::where('role', 0)->exists()) {
        User::create([
            'CIN' => '00000000',
            'nom' => 'Admin',
            'prenom' => 'Principal',
            'dateNaissance' => '2001-01-01',
            'ville' => 'AdminVille',
            'motDePasse' => Hash::make('admin123'),
            'role' => 0,
        ]);
    }

    }
}
