<?php

namespace App\Domain\Transaction\Enums;

enum TransactionType: string {
    case REVENU         = 'revenu';
    case DEPENSE        = 'depense';
    case EPARGNE        = 'epargne';
    case PRET           = 'pret';
    case REMBOURSEMENT  = 'remboursement';

    public function label(): string {
        return match($this) {
            self::REVENU        => 'Revenu',
            self::DEPENSE       => 'Dépense',
            self::EPARGNE       => 'Épargne',
            self::PRET          => 'Prêt accordé',
            self::REMBOURSEMENT => 'Remboursement reçu',
        };
    }

    public function isDebit(): bool {
        return in_array($this, [self::DEPENSE, self::PRET]);
    }
}
