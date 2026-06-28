<?php

namespace App\Domain\Goal\Models;

use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id', 'nom', 'description', 'montant_cible',
        'montant_actuel', 'date_cible', 'atteint',
    ];

    protected $casts = [
        'montant_cible' => 'decimal:2',
        'montant_actuel' => 'decimal:2',
        'date_cible' => 'date',
        'atteint' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
