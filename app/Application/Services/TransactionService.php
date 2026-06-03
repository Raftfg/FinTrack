<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateTransactionData;
use App\Domain\Transaction\Events\TransactionCreated;
use App\Domain\Transaction\Models\Transaction;
use App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TransactionService {

    public function __construct(
        private readonly TransactionRepositoryInterface $repository,
    ) {}

    public function create(CreateTransactionData $data): Transaction {
        $transaction = $this->repository->create($data->toArray());
        event(new TransactionCreated($transaction));
        $this->invalidateCache($data->user_id);
        return $transaction;
    }

    public function delete(int $id, int $userId): void {
        $transaction = Transaction::where('user_id', $userId)->findOrFail($id);
        $transaction->delete();
        $this->invalidateCache($userId);
    }

    private function invalidateCache(int $userId): void {
        Cache::forget("dashboard_stats_{$userId}");
        Cache::forget("budget_stats_{$userId}_" . now()->month . '_' . now()->year);
    }
}
