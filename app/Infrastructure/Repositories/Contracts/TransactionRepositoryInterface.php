<?php

namespace App\Infrastructure\Repositories\Contracts;

use App\Domain\Transaction\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;

    public function getMonthlyByType(int $userId, int $month, int $year): array;

    public function getMonthlyByCategory(int $userId, int $month, int $year): array;

    public function paginateForUser(int $userId, array $filters, int $perPage = 20);

    public function deleteById(int $id): bool;
}
