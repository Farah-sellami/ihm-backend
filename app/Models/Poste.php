<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    use HasFactory;
    protected $fillable = ['titre','photos','description','prixIniale','duree','estApprouvé','scategorieID'];
    public function scategorie()
        {
        return $this->belongsTo(Scategorie::class,"scategorieID");
        }
        public function offres()
        {
            return $this->hasMany(Offre::class, 'poste_id');
        }
}
