<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;
    protected $fillable = [
        'montant',
        'dateEnchere',
        'poste_id', // ID du poste associé
    ];
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }
}
