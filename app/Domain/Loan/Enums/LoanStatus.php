<?php

namespace App\Domain\Loan\Enums;

enum LoanStatus: string {
    case NON_REMBOURSE = 'non_rembourse';
    case PARTIEL       = 'partiel';
    case REMBOURSE     = 'rembourse';

    public function label(): string {
        return match($this) {
            self::NON_REMBOURSE => 'Non remboursé',
            self::PARTIEL       => 'Partiellement remboursé',
            self::REMBOURSE     => 'Remboursé',
        };
    }

    public function color(): string {
        return match($this) {
            self::NON_REMBOURSE => 'red',
            self::PARTIEL       => 'amber',
            self::REMBOURSE     => 'green',
        };
    }
}
