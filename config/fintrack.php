<?php
return [
    'currency'        => env('APP_CURRENCY', 'XOF'),
    'currency_symbol' => env('APP_CURRENCY_SYMBOL', 'FCFA'),
    'budget_alert_threshold' => 80, // % avant alerte orange
    'transaction_categories' => [
        'nourriture', 'transport', 'sante', 'logement',
        'education', 'aide_sociale', 'epargne', 'loisirs', 'autre',
    ],
];
