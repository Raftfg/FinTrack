<?php

namespace App\Domain\Budget\Models;

use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id', 'categorie', 'montant_limite',
        'mois', 'annee',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
