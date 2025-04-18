<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'photos', 'description', 'prixIniale', 'duree', 'estApprouvé', 'scategorieID',  'user_id'];

       // Définir l'attribut photos comme un tableau
       protected $casts = [
        'photos' => 'array',
    ];
    
    public function scategorie()
    {
        return $this->belongsTo(Scategorie::class, "scategorieID");
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorieID');
    }

    public function offres()
    {
        return $this->hasMany(Offre::class, 'poste_id');
    }

    // Add the query scope for filtering by scategorieID
    public function scopeFilterByScategorie($query, $scategorieID)
    {
        return $query->where('scategorieID', $scategorieID);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
