<?php

namespace App\Domain\Transaction\Observers;

use App\Domain\Budget\Models\Budget;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use App\Notifications\SystemAlert;
use Illuminate\Support\Facades\Cache;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->invalidateCache($transaction);

        // Check if transaction is an expense
        if ($transaction->type === TransactionType::DEPENSE) {
            $month = $transaction->date_transaction->month;
            $year = $transaction->date_transaction->year;

            // Find matching budget for this month/year/category
            $budget = Budget::where('user_id', $transaction->user_id)
                ->where('categorie', $transaction->categorie)
                ->where('mois', $month)
                ->where('annee', $year)
                ->first();

            if ($budget) {
                // Calculate total spent in this category for this month
                $totalSpent = Transaction::where('user_id', $transaction->user_id)
                    ->where('type', TransactionType::DEPENSE)
                    ->where('categorie', $transaction->categorie)
                    ->whereMonth('date_transaction', $month)
                    ->whereYear('date_transaction', $year)
                    ->sum('montant');

                $limit = $budget->montant_limite;
                $currencySymbol = $transaction->user->currency_symbol ?? 'FCFA';

                if ($totalSpent >= $limit) {
                    // 100% Exceeded Alert
                    $transaction->user->notify(new SystemAlert(
                        "Budget Dépassé ! 🚨",
                        "Votre budget pour la catégorie '{$transaction->categorie}' ce mois-ci est entièrement consommé ou dépassé (" . number_format($totalSpent, 0, ',', ' ') . " / " . number_format($limit, 0, ',', ' ') . " " . $currencySymbol . ").",
                        'error'
                    ));
                } elseif ($totalSpent >= ($limit * 0.8)) {
                    // 80% Alert
                    $transaction->user->notify(new SystemAlert(
                        "Alerte de Budget (80%) ⚠️",
                        "Vous avez consommé plus de 80% de votre budget pour la catégorie '{$transaction->categorie}' ce mois-ci (" . number_format($totalSpent, 0, ',', ' ') . " / " . number_format($limit, 0, ',', ' ') . " " . $currencySymbol . ").",
                        'warning'
                    ));
                }
            }
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $this->invalidateCache($transaction);
    }

    /**
     * Invalidate caches associated with the transaction's user and period.
     */
    private function invalidateCache(Transaction $transaction): void
    {
        $month = $transaction->date_transaction->month;
        $year = $transaction->date_transaction->year;

        Cache::forget("dashboard_stats_{$transaction->user_id}");
        Cache::forget("budget_stats_{$transaction->user_id}_{$month}_{$year}");
    }
}
