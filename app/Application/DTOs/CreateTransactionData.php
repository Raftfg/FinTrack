<?php

namespace App\Application\DTOs;

use App\Domain\Transaction\Enums\TransactionType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class CreateTransactionData extends Data {
    public function __construct(
        public readonly int            $user_id,
        public readonly TransactionType $type,
        public readonly float          $montant,
        public readonly string         $categorie,
        public readonly \Carbon\Carbon $date_transaction,
        public readonly ?string        $note = null,
        public readonly ?int           $loan_id = null,
    ) {}
}
