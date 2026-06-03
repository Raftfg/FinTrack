<?php

namespace App\Application\Services;

use App\Domain\Goal\Models\Goal;
use App\Domain\Loan\Models\Loan;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getStats(int $userId): array
    {
        return Cache::remember(
            "dashboard_stats_{$userId}",
            now()->addMinutes(15),
            fn () => $this->computeStats($userId)
        );
    }

    private function computeStats(int $userId): array
    {
        $revenus = Transaction::forUser($userId)
            ->currentMonth()->ofType(TransactionType::REVENU)
            ->sum('montant');

        $depenses = Transaction::forUser($userId)
            ->currentMonth()->ofType(TransactionType::DEPENSE)
            ->sum('montant');

        $epargne = Transaction::forUser($userId)
            ->ofType(TransactionType::EPARGNE)->sum('montant');

        $pretsActifs = Loan::where('user_id', $userId)
            ->whereIn('statut', ['non_rembourse', 'partiel'])
            ->selectRaw('SUM(montant_initial - montant_rembourse) as total')
            ->value('total') ?? 0;

        $depensesParCategorie = Transaction::forUser($userId)
            ->currentMonth()->ofType(TransactionType::DEPENSE)
            ->selectRaw('categorie, SUM(montant) as total')
            ->groupBy('categorie')
            ->pluck('total', 'categorie')
            ->toArray();

        // Collections transformées en tableaux de primitives pour être sérialisables
        $dernieresTransactions = Transaction::forUser($userId)
            ->with('loan')
            ->orderByDesc('date_transaction')
            ->limit(5)->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'type' => $t->type->value,
                'type_label' => $t->type->label(),
                'is_debit' => $t->type->isDebit(),
                'montant' => $t->montant,
                'categorie' => $t->categorie,
                'date_transaction' => $t->date_transaction->format('d M'),
                'note' => $t->note,
            ])->toArray();

        $objectifs = Goal::where('user_id', $userId)
            ->where('atteint', false)
            ->get()
            ->map(fn ($g) => [
                'id' => $g->id,
                'nom' => $g->nom,
                'montant_cible' => $g->montant_cible,
                'montant_actuel' => $g->montant_actuel,
                'pct' => $g->montant_cible > 0
                    ? min(100, (int) (($g->montant_actuel / $g->montant_cible) * 100))
                    : 0,
            ])->toArray();

        return compact(
            'revenus', 'depenses', 'epargne', 'pretsActifs',
            'depensesParCategorie', 'dernieresTransactions', 'objectifs'
        );
    }
}
