<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface {

    public function create(array $data): Transaction {
        return Transaction::create($data);
    }

    public function getMonthlyByType(int $userId, int $month, int $year): array {
        return Transaction::forUser($userId)
            ->whereMonth('date_transaction', $month)
            ->whereYear('date_transaction', $year)
            ->selectRaw('type, SUM(montant) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
    }

    public function getMonthlyByCategory(int $userId, int $month, int $year): array {
        return Transaction::forUser($userId)
            ->whereMonth('date_transaction', $month)
            ->whereYear('date_transaction', $year)
            ->where('type', TransactionType::DEPENSE)
            ->selectRaw('categorie, SUM(montant) as total')
            ->groupBy('categorie')
            ->orderByDesc('total')
            ->pluck('total', 'categorie')
            ->toArray();
    }

    public function paginateForUser(int $userId, array $filters, int $perPage = 20) {
        $query = Transaction::forUser($userId)
            ->with('loan')
            ->orderByDesc('date_transaction');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['categorie'])) {
            $query->where('categorie', $filters['categorie']);
        }
        if (!empty($filters['mois'])) {
            $query->whereMonth('date_transaction', $filters['mois']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function deleteById(int $id): bool {
        return Transaction::findOrFail($id)->delete();
    }
}
