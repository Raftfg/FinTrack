<?php

return [
    'currency' => env('APP_CURRENCY', 'XOF'),
    'currency_symbol' => env('APP_CURRENCY_SYMBOL', 'FCFA'),
    'budget_alert_threshold' => 80, // % avant alerte orange
    'transaction_categories' => [
        'Alimentation',
        'Transport',
        'Santé',
        'Logement',
        'Éducation',
        'Aide Sociale',
        'Épargne',
        'Loisirs',
        'Autre',
    ],
];
