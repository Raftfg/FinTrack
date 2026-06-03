<?php

namespace App\Domain\Transaction\Observers;

use App\Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class TransactionObserver {

    public function created(Transaction $transaction): void {
        Cache::forget("dashboard_stats_{$transaction->user_id}");
    }

    public function deleted(Transaction $transaction): void {
        Cache::forget("dashboard_stats_{$transaction->user_id}");
    }
}
