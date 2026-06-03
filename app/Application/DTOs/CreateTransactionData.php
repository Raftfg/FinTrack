<?php

namespace App\Application\DTOs;

use App\Domain\Transaction\Enums\TransactionType;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class CreateTransactionData extends Data
{
    public function __construct(
        public readonly int $user_id,
        public readonly TransactionType $type,
        public readonly float $montant,
        public readonly string $categorie,
        public readonly Carbon $date_transaction,
        public readonly ?string $note = null,
        public readonly ?int $loan_id = null,
    ) {}
}
