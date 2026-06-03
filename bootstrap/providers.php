<?php

use App\Providers\AppServiceProvider;
use Barryvdh\DomPDF\ServiceProvider;
use Spatie\Honeypot\HoneypotServiceProvider;

return [
    AppServiceProvider::class,
    ServiceProvider::class,
    HoneypotServiceProvider::class,
];
