<?php

namespace App\Domain\Loan\Models;

use App\Domain\Loan\Enums\LoanStatus;
use App\Domain\Loan\Observers\LoanObserver;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'personne', 'email', 'telephone', 'montant_initial',
        'montant_rembourse', 'statut', 'date_pret',
        'date_echeance', 'note',
    ];

    protected $casts = [
        'statut' => LoanStatus::class,
        'montant_initial' => 'decimal:2',
        'montant_rembourse' => 'decimal:2',
        'date_pret' => 'date',
        'date_echeance' => 'date',
    ];

    protected static function booted(): void
    {
        static::observe(LoanObserver::class);
    }

    public function getMontantRestantAttribute(): float
    {
        return $this->montant_initial - $this->montant_rembourse;
    }

    public function getProgressionAttribute(): int
    {
        if ($this->montant_initial == 0) {
            return 0;
        }

        return (int) (($this->montant_rembourse / $this->montant_initial) * 100);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions()
    {
        return $this->hasMany(
            Transaction::class
        );
    }
}
