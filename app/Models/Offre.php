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
        'poste_id',
        'user_id',
    ];
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }
    public function scopeFilterByCategorie($query, $categorieId, $scategorieId = null)
    {
        $query->whereHas('poste', function ($query) use ($categorieId, $scategorieId) {
            $query->where('categorieID', $categorieId);

            if ($scategorieId) {
                $query->where('scategorieID', $scategorieId);
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeFilterByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }


}


