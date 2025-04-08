<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'CIN', 'nom', 'prenom', 'dateNaissance', 'ville', 'photoProfil', 'motDePasse', 'role', 'type', 'is_blocked'
    ];

    protected $hidden = [
        'motDePasse',
    ];

    // Ensure Laravel uses 'motDePasse' as the password column
    public function getAuthPassword()
    {
        return $this->motDePasse;
    }

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}

