<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FinTrack — Gestionnaire Financier Personnel Intelligent</title>
        
        <!-- Fonts / Icons -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background: radial-gradient(circle at 100% 0%, #e5eeff 0%, #f8f9ff 50%, #eff4ff 100%);
                font-family: 'Inter', sans-serif;
                overflow-x: hidden;
            }
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
        </style>
    </head>
    <body class="text-on-background min-h-screen antialiased flex flex-col justify-between selection:bg-primary-fixed selection:text-primary">

        <!-- Top Header Navigation -->
        <header class="sticky top-0 w-full z-50 glass border-b border-outline-variant/20 transition-all duration-300">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <!-- Brand Logo -->
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[20px] font-bold">account_balance_wallet</span>
                    </div>
                    <span class="font-headline-sm text-headline-sm text-primary font-bold tracking-tight">FinTrack</span>
                </a>

                <!-- Navigation Auth Links -->
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-primary text-on-primary hover:bg-primary-container hover:text-on-primary-container rounded-xl text-label-md font-semibold transition-all shadow-md active:scale-95">
                            Mon Tableau de Bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-primary hover:text-primary-fixed-dim font-semibold text-label-md transition-colors">
                            Se connecter
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2 bg-primary text-on-primary hover:bg-primary-container rounded-xl text-label-md font-semibold transition-all shadow-md active:scale-95">
                                S'inscrire
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Main Hero Section -->
        <main class="flex-grow">
            <!-- Hero Content -->
            <section class="max-w-6xl mx-auto px-6 pt-16 pb-24 text-center md:text-left flex flex-col md:flex-row items-center gap-lg">
                <div class="flex-1 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-secondary-container/40 border border-secondary/20 rounded-full text-on-secondary-container text-label-sm font-semibold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        Gestion Budgétaire & Sécurité Renforcée
                    </div>
                    <h1 class="text-display-lg-mobile md:text-display-lg text-on-surface leading-tight tracking-tight font-extrabold">
                        Maîtrisez votre destin <span class="text-primary">financier</span> en toute simplicité
                    </h1>
                    <p class="text-body-lg text-on-surface-variant font-normal max-w-xl">
                        Suivez vos revenus et dépenses en temps réel, économisez pour vos projets de vie, gérez vos prêts et planifiez des relances automatiques pour vos débiteurs.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-md pt-md justify-center md:justify-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-primary text-on-primary hover:bg-primary-container rounded-xl font-label-md text-label-md font-bold text-center shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                                Accéder à mon espace
                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-primary text-on-primary hover:bg-primary-container rounded-xl font-label-md text-label-md font-bold text-center shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                                Commencer gratuitement
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-surface-container border border-outline-variant/30 text-on-surface hover:bg-surface-container-high rounded-xl font-label-md text-label-md font-bold text-center transition-all active:scale-95">
                                Découvrir la démo
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Visual Element -->
                <div class="flex-1 w-full relative flex justify-center">
                    <div class="w-full max-w-[500px] h-[380px] bg-gradient-to-tr from-primary to-secondary rounded-3xl shadow-2xl relative overflow-hidden flex flex-col justify-between p-lg text-white group hover:scale-[1.02] transition-transform duration-500">
                        <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-xl"></div>
                        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-black/10 rounded-full blur-2xl"></div>

                        <!-- Card Header -->
                        <div class="flex justify-between items-start z-10">
                            <div>
                                <p class="text-white/70 text-label-sm font-medium uppercase tracking-wider">Solde Total Estimé</p>
                                <h3 class="text-display-lg-mobile font-bold tracking-tight mt-xs">1 248 500 FCFA</h3>
                            </div>
                            <span class="material-symbols-outlined text-4xl opacity-80">credit_card</span>
                        </div>

                        <!-- Card Body / Mini-Stats UI Mockup -->
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-md border border-white/15 space-y-sm z-10 shadow-sm">
                            <div class="flex justify-between items-center text-body-sm">
                                <span class="flex items-center gap-2 font-medium">
                                    <span class="material-symbols-outlined text-[18px] text-secondary-fixed">trending_up</span>
                                    Revenus (Salaire Juin)
                                </span>
                                <span class="font-bold">+850 000 FCFA</span>
                            </div>
                            <div class="w-full h-px bg-white/10"></div>
                            <div class="flex justify-between items-center text-body-sm">
                                <span class="flex items-center gap-2 font-medium">
                                    <span class="material-symbols-outlined text-[18px] text-error-container">trending_down</span>
                                    Dépenses (Logement & Alimentation)
                                </span>
                                <span class="font-bold">-320 000 FCFA</span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="flex justify-between items-center text-label-sm text-white/80 z-10">
                            <span>FinTrack App v1.2</span>
                            <span class="px-3 py-1 bg-white/15 rounded-full text-[10px] font-bold uppercase">100% Sécurisé</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Grid Section -->
            <section class="bg-surface-container-lowest border-y border-outline-variant/20 py-24">
                <div class="max-w-6xl mx-auto px-6">
                    <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                        <h2 class="text-headline-lg font-bold text-on-surface">Pourquoi choisir FinTrack ?</h2>
                        <p class="text-body-md text-on-surface-variant">Tout ce dont vous avez besoin pour analyser et maîtriser votre portefeuille budgétaire sans prise de tête.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                        <!-- Feature 1 -->
                        <div class="p-lg bg-background rounded-2xl border border-outline-variant/30 hover:shadow-md hover:border-primary/20 transition-all flex flex-col justify-between h-[240px]">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-md">
                                    <span class="material-symbols-outlined text-[24px]">payments</span>
                                </div>
                                <h3 class="text-headline-sm font-semibold text-on-surface mb-xs">Budget Dynamique</h3>
                                <p class="text-body-sm text-on-surface-variant">Catégorisez automatiquement vos revenus et vos dépenses avec des indicateurs visuels colorés.</p>
                            </div>
                            <span class="text-primary font-semibold text-label-sm flex items-center gap-1">En savoir plus <span class="material-symbols-outlined text-[16px]">chevron_right</span></span>
                        </div>

                        <!-- Feature 2 -->
                        <div class="p-lg bg-background rounded-2xl border border-outline-variant/30 hover:shadow-md hover:border-primary/20 transition-all flex flex-col justify-between h-[240px]">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-md">
                                    <span class="material-symbols-outlined text-[24px]">notifications_active</span>
                                </div>
                                <h3 class="text-headline-sm font-semibold text-on-surface mb-xs">Rappels Automatisés</h3>
                                <p class="text-body-sm text-on-surface-variant">Planifiez l'envoi de rappels e-mail et SMS à vos débiteurs pour vos prêts échus en un clin d'œil.</p>
                            </div>
                            <span class="text-secondary font-semibold text-label-sm flex items-center gap-1">En savoir plus <span class="material-symbols-outlined text-[16px]">chevron_right</span></span>
                        </div>

                        <!-- Feature 3 -->
                        <div class="p-lg bg-background rounded-2xl border border-outline-variant/30 hover:shadow-md hover:border-primary/20 transition-all flex flex-col justify-between h-[240px]">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center mb-md">
                                    <span class="material-symbols-outlined text-[24px]">verified_user</span>
                                </div>
                                <h3 class="text-headline-sm font-semibold text-on-surface mb-xs">Sécurité Avancée</h3>
                                <p class="text-body-sm text-on-surface-variant">Formulaires protégés par Honeypot, limitation de requêtes, en-têtes HTTP de sécurité et sauvegardes SQL.</p>
                            </div>
                            <span class="text-tertiary font-semibold text-label-sm flex items-center gap-1">En savoir plus <span class="material-symbols-outlined text-[16px]">chevron_right</span></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Call To Action Section -->
            <section class="max-w-6xl mx-auto px-6 py-24 text-center">
                <div class="bg-gradient-to-tr from-surface-container to-surface-container-high rounded-3xl p-lg md:p-20 shadow-sm border border-outline-variant/20 flex flex-col items-center space-y-6">
                    <h2 class="text-headline-lg font-bold text-on-surface max-w-xl leading-tight">
                        Prêt à reprendre le contrôle de vos finances ?
                    </h2>
                    <p class="text-body-md text-on-surface-variant max-w-lg">
                        Rejoignez FinTrack aujourd'hui. Créez un compte gratuit et commencez à suivre vos transactions en moins de deux minutes.
                    </p>
                    <div class="pt-sm">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-primary text-on-primary hover:bg-primary-container rounded-xl font-label-md text-label-md font-bold transition-all active:scale-95">
                                Accéder à mon tableau de bord
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-primary text-on-primary hover:bg-primary-container rounded-xl font-label-md text-label-md font-bold transition-all active:scale-95">
                                Créer mon compte gratuit
                            </a>
                        @endauth
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="w-full bg-surface-container-lowest border-t border-outline-variant/20 py-8">
            <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-md text-label-sm text-on-surface-variant">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-primary text-on-primary flex items-center justify-center font-bold">F</div>
                    <span class="font-semibold text-on-surface">FinTrack &copy; {{ date('Y') }}</span>
                </div>
                <div class="flex items-center gap-6 font-medium">
                    <a href="https://laravel.com" target="_blank" class="hover:text-primary transition-colors">Laravel 13</a>
                    <a href="#" class="hover:text-primary transition-colors">Conditions d'utilisation</a>
                    <a href="#" class="hover:text-primary transition-colors">Confidentialité</a>
                </div>
            </div>
        </footer>

    </body>
</html>
