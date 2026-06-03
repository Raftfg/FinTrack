<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FinTrack') }} - Authentification</title>

        <!-- Fonts / Icons -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background: radial-gradient(circle at 100% 0%, #e5eeff 0%, #f8f9ff 50%, #eff4ff 100%);
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="text-on-background min-h-screen antialiased flex items-center justify-center p-6 selection:bg-primary-fixed selection:text-primary">
        <div class="w-full max-w-md flex flex-col gap-lg">
            <!-- Brand Header -->
            <div class="text-center">
                <a href="/" class="inline-flex items-center gap-2 group">
                    <div class="w-11 h-11 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[24px] font-bold">account_balance_wallet</span>
                    </div>
                    <span class="font-headline-lg-mobile text-headline-lg-mobile text-primary font-bold tracking-tight">FinTrack</span>
                </a>
            </div>

            <!-- Auth Form Card -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl shadow-xl p-lg md:p-xl space-y-lg">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <div class="text-center text-label-sm text-on-surface-variant font-medium">
                <a href="/" class="hover:text-primary transition-colors flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </body>
</html>
