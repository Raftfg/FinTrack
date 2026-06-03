<?php

namespace App\Domain\Loan\Observers;

use App\Domain\Loan\Models\Loan;
use App\Domain\Loan\Enums\LoanStatus;

class LoanObserver {

    public function updated(Loan $loan): void {
        if ($loan->isDirty('montant_rembourse')) {
            $restant = $loan->montant_initial - $loan->montant_rembourse;

            $loan->statut = match(true) {
                $restant <= 0                => LoanStatus::REMBOURSE,
                $loan->montant_rembourse > 0 => LoanStatus::PARTIEL,
                default                      => LoanStatus::NON_REMBOURSE,
            };

            $loan->saveQuietly();
        }
    }
}
