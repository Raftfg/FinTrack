<?php

namespace App\Domain\Transaction\Models;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id','type','montant','categorie',
        'date_transaction','note','loan_id',
    ];

    protected $casts = [
        'type'             => TransactionType::class,
        'montant'          => 'decimal:2',
        'date_transaction' => 'date',
    ];

    protected static function booted(): void {
        static::observe(TransactionObserver::class);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function loan() {
        return $this->belongsTo(\App\Domain\Loan\Models\Loan::class);
    }

    public function scopeForUser($query, int $userId) {
        return $query->where('user_id', $userId);
    }

    public function scopeCurrentMonth($query) {
        return $query->whereMonth('date_transaction', now()->month)
                     ->whereYear('date_transaction', now()->year);
    }

    public function scopeOfType($query, TransactionType $type) {
        return $query->where('type', $type);
    }
}
