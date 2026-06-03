<?php

namespace App\Domain\Transaction\Events;

use App\Domain\Transaction\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Transaction $transaction
    ) {}
}
