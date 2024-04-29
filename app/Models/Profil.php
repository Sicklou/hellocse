<?php

namespace App\Models;

use App\Models\Enum\ProfilStatut;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'image',
        'statut',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'statut' => ProfilStatut::class,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('statut', ProfilStatut::Actif);
    }
}
