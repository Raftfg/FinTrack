<?php

namespace App\Domain\Budget\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'categorie', 'montant_limite',
        'mois', 'annee'
    ];

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}
