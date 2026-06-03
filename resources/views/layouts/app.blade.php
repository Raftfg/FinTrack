<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name', 'Financely') }} - @yield('title')</title>
    
    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body {
            background-color: #f8f9ff;
            font-family: 'Inter', sans-serif;
            min-height: max(884px, 100dvh);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen" x-data="{ showTransactionModal: false, showLoanModal: false, showGoalModal: false, selectedGoal: null, showGoalDetailModal: false, showLoanEditModal: false, selectedLoan: null, showGoalAddSavings: false, savingsAmount: '', showTransactionEditModal: false, selectedTransaction: null, showGoalEditModal: false, showNotifications: false }">

    <!-- Sidebar for desktop -->
    <aside class="hidden md:flex md:flex-col md:w-64 md:fixed md:inset-y-0 md:left-0 bg-surface-container-low dark:bg-inverse-surface border-r border-outline-variant/30 z-50">
        <!-- Brand/Logo -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-outline-variant/20">
            <h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim flex items-center gap-2">
                <span class="material-symbols-outlined text-[28px]">account_balance_wallet</span>
                FinTrack
            </h1>
            <button @click="showNotifications = true" class="relative w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors active:scale-95 duration-100" title="Notifications">
                <span class="material-symbols-outlined text-primary text-[22px]">notifications</span>
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-error text-on-error rounded-full text-[9px] font-bold flex items-center justify-center">{{ $unreadNotificationsCount }}</span>
                @endif
            </button>
        </div>
        
        <!-- Action Button for Desktop -->
        <div class="px-4 pt-4 relative" x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-center gap-2 py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-all duration-200 rounded-xl font-label-md text-label-md shadow-md active:scale-95">
                <span class="material-symbols-outlined">add_circle</span>
                Ajouter...
            </button>
            
            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition.origin.top class="absolute left-4 right-4 mt-2 bg-surface-container rounded-xl border border-outline-variant/30 shadow-xl overflow-hidden z-50">
                <button @click="showTransactionModal = true; open = false" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-surface-container-high transition-colors text-left text-body-md text-on-surface">
                    <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
                    Transaction
                </button>
                <button @click="showLoanModal = true; open = false" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-surface-container-high transition-colors text-left text-body-md text-on-surface">
                    <span class="material-symbols-outlined text-secondary text-[20px]">account_balance</span>
                    Prêt
                </button>
                <button @click="showGoalModal = true; open = false" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-surface-container-high transition-colors text-left text-body-md text-on-surface">
                    <span class="material-symbols-outlined text-tertiary text-[20px]">flag</span>
                    Objectif
                </button>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow px-4 py-6 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group active:scale-98
                {{ request()->routeIs('dashboard') 
                    ? 'bg-primary text-on-primary shadow-md' 
                    : 'text-on-surface-variant hover:bg-surface-container-high hover:text-primary dark:hover:text-primary-fixed-dim' }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('dashboard') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>dashboard</span>
                <span class="font-label-md text-label-md">Dashboard</span>
            </a>
            
            <!-- Transactions -->
            <a href="{{ route('transactions.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group active:scale-98
                {{ request()->routeIs('transactions.*') 
                    ? 'bg-primary text-on-primary shadow-md' 
                    : 'text-on-surface-variant hover:bg-surface-container-high hover:text-primary dark:hover:text-primary-fixed-dim' }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('transactions.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>receipt_long</span>
                <span class="font-label-md text-label-md">Transactions</span>
            </a>
            
            <!-- Loans -->
            <a href="{{ route('loans.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group active:scale-98
                {{ request()->routeIs('loans.*') 
                    ? 'bg-primary text-on-primary shadow-md' 
                    : 'text-on-surface-variant hover:bg-surface-container-high hover:text-primary dark:hover:text-primary-fixed-dim' }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('loans.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>account_balance</span>
                <span class="font-label-md text-label-md">Prêts</span>
            </a>
            
            <!-- Goals -->
            <a href="{{ route('goals.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group active:scale-98
                {{ request()->routeIs('goals.*') 
                    ? 'bg-primary text-on-primary shadow-md' 
                    : 'text-on-surface-variant hover:bg-surface-container-high hover:text-primary dark:hover:text-primary-fixed-dim' }}">
                <span class="material-symbols-outlined" {!! request()->routeIs('goals.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>flag</span>
                <span class="font-label-md text-label-md">Objectifs</span>
            </a>
        </nav>
        
        <!-- User Profile Footer -->
        <div class="p-4 border-t border-outline-variant/20 bg-surface-container dark:bg-inverse-surface/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary-fixed">
                        <img alt="Profil" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Alex') }}&background=00288e&color=fff"/>
                    </div>
                    <div class="truncate max-w-[120px]">
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Bonjour,</p>
                        <p class="font-body-md text-body-md font-semibold text-on-surface truncate">{{ auth()->user()?->name ?? 'Alex' }}</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl text-outline hover:text-error hover:bg-error-container transition-colors active:scale-95 duration-100" title="Déconnexion">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            </div>
            <div class="text-[10px] text-center text-outline-variant mt-2 font-medium">
                Réalisé par Dynamic Technology Group
            </div>
        </div>
    </aside>

    <div class="min-h-screen flex flex-col md:pl-64">
        <!-- TopAppBar (Mobile only) -->
        <header class="bg-surface dark:bg-on-surface shadow-sm docked full-width top-0 flex justify-between items-center px-margin-mobile h-16 w-full z-50 sticky md:hidden">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary-fixed">
                    <img alt="Profil" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Alex') }}&background=00288e&color=fff"/>
                </div>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Bonjour, {{ auth()->user()?->name ?? 'Alex' }} !</p>
                    <h1 class="font-headline-sm text-headline-sm font-bold text-primary dark:text-primary-fixed-dim">FinTrack</h1>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <button @click="showNotifications = true" class="relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container-high dark:hover:bg-inverse-surface transition-colors active:scale-95 duration-100" title="Notifications">
                    <span class="material-symbols-outlined text-primary dark:text-primary-fixed-dim">notifications</span>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span class="absolute top-1 right-1 w-5 h-5 bg-error text-on-error rounded-full text-[10px] font-bold flex items-center justify-center">{{ $unreadNotificationsCount }}</span>
                    @endif
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-error-container text-error transition-colors active:scale-95 duration-100">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="px-margin-mobile pt-lg pb-24 md:pb-8 max-w-lg md:max-w-6xl mx-auto w-full space-y-lg flex-grow">
            @yield('content')
        </main>
    </div>

    <!-- BottomNavBar (Mobile only) -->
    <nav class="bg-surface-container dark:bg-inverse-surface docked full-width bottom-0 rounded-t-xl shadow-lg fixed bottom-0 w-full z-50 flex justify-around items-center h-20 pb-2 px-4 md:hidden">
        <!-- Dashboard -->
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('dashboard') ? 'bg-primary-container dark:bg-primary text-on-primary-container dark:text-on-primary rounded-full px-4 py-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed' }} active:scale-90 transition-transform" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined" {!! request()->routeIs('dashboard') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>dashboard</span>
            <span class="font-label-sm text-label-sm">Dashboard</span>
        </a>
        <!-- Transactions -->
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('transactions.*') ? 'bg-primary-container dark:bg-primary text-on-primary-container dark:text-on-primary rounded-full px-4 py-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed' }} active:scale-90 transition-transform" href="{{ route('transactions.index') }}">
            <span class="material-symbols-outlined" {!! request()->routeIs('transactions.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>receipt_long</span>
            <span class="font-label-sm text-label-sm">Transactions</span>
        </a>
        <!-- Loans -->
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('loans.*') ? 'bg-primary-container dark:bg-primary text-on-primary-container dark:text-on-primary rounded-full px-4 py-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed' }} active:scale-90 transition-transform" href="{{ route('loans.index') }}">
            <span class="material-symbols-outlined" {!! request()->routeIs('loans.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>account_balance</span>
            <span class="font-label-sm text-label-sm">Prêts</span>
        </a>
        <!-- Goals -->
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('goals.*') ? 'bg-primary-container dark:bg-primary text-on-primary-container dark:text-on-primary rounded-full px-4 py-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed' }} active:scale-90 transition-transform" href="{{ route('goals.index') }}">
            <span class="material-symbols-outlined" {!! request()->routeIs('goals.*') ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>flag</span>
            <span class="font-label-sm text-label-sm">Objectifs</span>
        </a>
    </nav>

    <!-- Global FAB (Mobile only) -->
    <div class="md:hidden fixed bottom-24 right-6 z-40" x-data="{ open: false }">
        <button @click="open = !open" class="w-14 h-14 bg-primary text-on-primary rounded-2xl shadow-xl flex items-center justify-center active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-[32px] transition-transform duration-200" :class="open ? 'rotate-45' : ''">add</span>
        </button>
        <div x-show="open" @click.away="open = false" x-transition.scale.origin.bottom.right class="absolute right-0 bottom-16 bg-surface-container rounded-2xl border border-outline-variant/30 shadow-2xl p-sm flex flex-col gap-xs min-w-[200px] z-50">
            <button @click="showTransactionModal = true; open = false" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container-high transition-colors rounded-xl text-left text-label-md font-semibold text-on-surface">
                <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
                Transaction
            </button>
            <button @click="showLoanModal = true; open = false" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container-high transition-colors rounded-xl text-left text-label-md font-semibold text-on-surface">
                <span class="material-symbols-outlined text-secondary text-[20px]">account_balance</span>
                Prêt
            </button>
            <button @click="showGoalModal = true; open = false" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container-high transition-colors rounded-xl text-left text-label-md font-semibold text-on-surface">
                <span class="material-symbols-outlined text-tertiary text-[20px]">flag</span>
                Objectif
            </button>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <div x-show="showTransactionModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showTransactionModal = false" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20">
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-2">Nouvelle Transaction</h3>
            <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
                @csrf
                @honeypot
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Type</label>
                    <select name="type" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                        <option value="revenu">Revenu</option>
                        <option value="depense">Dépense</option>
                        <option value="epargne">Épargne</option>
                        <option value="remboursement">Remboursement reçu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant (FCFA)</label>
                    <input type="number" step="0.01" min="0.01" name="montant" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Catégorie</label>
                    <input type="text" name="categorie" placeholder="ex: Alimentation, Salaire..." class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Date</label>
                    <input type="date" name="date_transaction" value="{{ date('Y-m-d') }}" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Note / Description</label>
                    <input type="text" name="note" placeholder="Optionnel" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                </div>
                <div class="flex gap-md pt-md">
                    <button type="button" @click="showTransactionModal = false" class="flex-grow py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
                        Annuler
                    </button>
                    <button type="submit" class="flex-grow py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md shadow-md">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Loan Modal -->
    <div x-show="showLoanModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showLoanModal = false" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20">
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-2">Nouveau Prêt</h3>
            <form method="POST" action="{{ route('loans.store') }}" class="space-y-4">
                @csrf
                @honeypot
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Nom du débiteur</label>
                    <input type="text" name="personne" placeholder="ex: Jean Dupont" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div class="grid grid-cols-2 gap-sm">
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Email débiteur</label>
                        <input type="email" name="email" placeholder="ex: jean@example.com" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Téléphone débiteur</label>
                        <input type="text" name="telephone" placeholder="ex: +22997000000" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                    </div>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant initial (FCFA)</label>
                    <input type="number" step="0.01" min="0.01" name="montant_initial" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant remboursé (FCFA)</label>
                    <input type="number" step="0.01" min="0" value="0.00" name="montant_rembourse" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Statut</label>
                    <select name="statut" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                        <option value="non_rembourse">Non remboursé</option>
                        <option value="partiel">Partiellement remboursé</option>
                        <option value="rembourse">Remboursé</option>
                    </select>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Date du prêt</label>
                    <input type="date" name="date_pret" value="{{ date('Y-m-d') }}" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Date d'échéance</label>
                    <input type="date" name="date_echeance" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Note / Description</label>
                    <input type="text" name="note" placeholder="Optionnel" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                </div>
                <div class="flex gap-md pt-md">
                    <button type="button" @click="showLoanModal = false" class="flex-grow py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
                        Annuler
                    </button>
                    <button type="submit" class="flex-grow py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md shadow-md">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Goal Modal -->
    <div x-show="showGoalModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showGoalModal = false" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20">
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-2">Nouvel Objectif</h3>
            <form method="POST" action="{{ route('goals.store') }}" class="space-y-4">
                @csrf
                @honeypot
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Nom de l'objectif</label>
                    <input type="text" name="nom" placeholder="ex: Achat maison, Épargne..." class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Description</label>
                    <input type="text" name="description" placeholder="Optionnel" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant cible (FCFA)</label>
                    <input type="number" step="0.01" min="0.01" name="montant_cible" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant actuel épargné (FCFA)</label>
                    <input type="number" step="0.01" min="0" value="0.00" name="montant_actuel" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Date cible</label>
                    <input type="date" name="date_cible" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                </div>
                <div class="flex gap-md pt-md">
                    <button type="button" @click="showGoalModal = false" class="flex-grow py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
                        Annuler
                    </button>
                    <button type="submit" class="flex-grow py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md shadow-md">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Goal Detail Modal -->
    <div x-show="showGoalDetailModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showGoalDetailModal = false; selectedGoal = null" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20" x-data="{
            getProgressPct() {
                if (!selectedGoal || !selectedGoal.montant_cible) return 0;
                return Math.min(100, Math.round((selectedGoal.montant_actuel / selectedGoal.montant_cible) * 100));
            },
            getRemaining() {
                if (!selectedGoal) return 0;
                return Math.max(0, selectedGoal.montant_cible - selectedGoal.montant_actuel);
            }
        }">
            <div x-show="selectedGoal">
                <div class="flex justify-between items-start mb-md">
                    <h3 class="font-headline-sm text-headline-sm font-bold text-primary" x-text="selectedGoal ? selectedGoal.nom : ''"></h3>
                    <button @click="showGoalDetailModal = false; selectedGoal = null" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
                
                <p class="font-body-md text-body-md text-on-surface-variant mb-lg" x-text="selectedGoal ? (selectedGoal.description || 'Aucune description fournie.') : ''"></p>

                <div class="space-y-md mb-lg">
                    <div class="flex justify-between text-body-md">
                        <span class="text-on-surface-variant font-semibold">Progression</span>
                        <span class="font-bold text-primary" x-text="getProgressPct() + '%'"></span>
                    </div>
                    <div class="w-full bg-surface-container h-3 rounded-full overflow-hidden">
                        <div class="bg-primary h-full rounded-full transition-all duration-500" :style="'width: ' + getProgressPct() + '%'"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-sm pt-sm border-t border-outline-variant/20">
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Épargné</p>
                            <p class="font-headline-sm text-headline-sm font-bold text-secondary" x-text="selectedGoal ? (new Intl.NumberFormat('fr-FR').format(selectedGoal.montant_actuel) + ' FCFA') : ''"></p>
                        </div>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Cible</p>
                            <p class="font-headline-sm text-headline-sm font-bold text-on-surface" x-text="selectedGoal ? (new Intl.NumberFormat('fr-FR').format(selectedGoal.montant_cible) + ' FCFA') : ''"></p>
                        </div>
                    </div>

                    <div class="pt-sm border-t border-outline-variant/20 text-body-md space-y-xs">
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Reste à épargner :</span>
                            <span class="font-bold text-tertiary" x-text="selectedGoal ? (new Intl.NumberFormat('fr-FR').format(getRemaining()) + ' FCFA') : ''"></span>
                        </div>
                        <div class="flex justify-between" x-show="selectedGoal && selectedGoal.date_cible">
                            <span class="text-on-surface-variant">Date limite :</span>
                            <span class="font-bold text-on-surface" x-text="selectedGoal && selectedGoal.date_cible ? new Date(selectedGoal.date_cible).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Statut :</span>
                            <span class="font-bold text-secondary" x-text="selectedGoal && selectedGoal.atteint ? 'Atteint' : 'En cours'"></span>
                        </div>
                    </div>
                </div>

                <!-- Add savings quick-form -->
                <div x-show="!showGoalAddSavings" class="flex gap-md pt-md">
                    <button type="button" @click="showGoalDetailModal = false; selectedGoal = null; showGoalAddSavings = false" class="flex-1 py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md font-semibold">
                        Fermer
                    </button>
                    <button type="button" @click="showGoalAddSavings = true; savingsAmount = ''" x-show="selectedGoal && !selectedGoal.atteint" class="flex-1 py-3 bg-secondary text-on-secondary hover:bg-secondary/80 transition-colors rounded-xl font-label-md text-label-md font-semibold flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        Ajouter épargne
                    </button>
                </div>
                <div x-show="showGoalAddSavings" x-transition class="pt-md border-t border-outline-variant/20 space-y-sm">
                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Montant à ajouter (FCFA)</p>
                    <template x-if="selectedGoal">
                        <form :action="'/goals/' + selectedGoal.id" method="POST" class="flex gap-sm">
                            @csrf
                            @honeypot
                            @method('PATCH')
                            <input type="number" name="ajout_epargne" x-model="savingsAmount" min="1" step="1" placeholder="ex: 5000" required class="flex-1 bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                            <button type="submit" class="px-4 py-2 bg-secondary text-on-secondary rounded-xl font-label-md text-label-md hover:bg-secondary/80 transition-colors active:scale-95">
                                <span class="material-symbols-outlined text-[20px]">check</span>
                            </button>
                            <button type="button" @click="showGoalAddSavings = false" class="px-3 py-2 bg-surface-container text-on-surface rounded-xl hover:bg-surface-container-high transition-colors">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Edit / Repayment Modal -->
    <div x-show="showLoanEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showLoanEditModal = false; selectedLoan = null" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-headline-sm text-headline-sm font-bold text-primary" x-text="selectedLoan ? 'Prêt — ' + selectedLoan.personne : 'Mettre à jour'"></h3>
                <button @click="showLoanEditModal = false; selectedLoan = null" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <template x-if="selectedLoan">
                <div class="space-y-md">
                    <!-- Summary -->
                    <div class="grid grid-cols-2 gap-sm">
                        <div class="bg-surface-container p-sm rounded-xl">
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Total dû</p>
                            <p class="font-headline-sm text-headline-sm font-bold text-primary" x-text="new Intl.NumberFormat('fr-FR').format(selectedLoan.montant_initial) + ' FCFA'"></p>
                        </div>
                        <div class="bg-surface-container p-sm rounded-xl">
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Remboursé</p>
                            <p class="font-headline-sm text-headline-sm font-bold text-secondary" x-text="new Intl.NumberFormat('fr-FR').format(selectedLoan.montant_rembourse) + ' FCFA'"></p>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div>
                        <div class="flex justify-between text-label-sm text-on-surface-variant mb-xs">
                            <span>Progression</span>
                            <span x-text="selectedLoan.montant_initial > 0 ? Math.round((selectedLoan.montant_rembourse / selectedLoan.montant_initial) * 100) + '%' : '0%'"></span>
                        </div>
                        <div class="w-full bg-surface-container-highest h-2 rounded-full overflow-hidden">
                            <div class="bg-secondary h-full rounded-full transition-all duration-500"
                                :style="'width: ' + (selectedLoan.montant_initial > 0 ? Math.min(100, Math.round((selectedLoan.montant_rembourse / selectedLoan.montant_initial) * 100)) : 0) + '%'"></div>
                        </div>
                    </div>
                    <!-- Repayment form -->
                    <form :action="'/loans/' + selectedLoan.id" method="POST" class="space-y-sm">
                        @csrf
                        @honeypot
                        @method('PATCH')
                        <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Mettre à jour le prêt</p>
                        <div class="flex gap-sm">
                            <input type="number" name="ajout_remboursement" min="1" step="1" placeholder="Montant reçu (FCFA)" class="flex-1 bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                            <select name="statut" class="bg-surface-container border border-outline-variant/30 rounded-xl px-3 py-2 text-body-md">
                                <option value="">Auto</option>
                                <option value="partiel">Partiel</option>
                                <option value="rembourse">Soldé</option>
                                <option value="non_rembourse">Non payé</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-sm">
                            <div>
                                <label class="block text-label-sm text-on-surface-variant mb-xs">Email débiteur</label>
                                <input type="email" name="email" x-model="selectedLoan.email" placeholder="ex: jean@example.com" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-3 py-1.5 text-body-sm">
                            </div>
                            <div>
                                <label class="block text-label-sm text-on-surface-variant mb-xs">Téléphone débiteur</label>
                                <input type="text" name="telephone" x-model="selectedLoan.telephone" placeholder="ex: +22997000000" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-3 py-1.5 text-body-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-sm pt-sm">
                            <button type="button" @click="showLoanEditModal = false; selectedLoan = null" class="py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
                                Annuler
                            </button>
                            <button type="submit" class="py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md shadow-md">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                    <!-- Delete -->
                    <form :action="'/loans/' + selectedLoan.id" method="POST" class="pt-xs border-t border-outline-variant/20">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Supprimer ce prêt ?')" class="w-full py-2 text-error hover:bg-error-container transition-colors rounded-xl font-label-sm text-label-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Supprimer ce prêt
                        </button>
                    </form>
                </div>
            </template>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div x-show="showNotifications" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showNotifications = false" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-headline-sm text-headline-sm font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-[24px]">notifications</span>
                    Notifications
                </h3>
                <button @click="showNotifications = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            
            <div class="space-y-sm max-h-[350px] overflow-y-auto pr-xs">
                @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                    @foreach($recentNotifications as $notification)
                        <div class="p-md bg-surface-container-low rounded-xl border border-outline-variant/10 relative group">
                            <div class="flex justify-between items-start gap-md mb-xs">
                                <p class="font-label-md text-label-md font-bold text-on-surface">
                                    {{ $notification->data['title'] ?? 'Alerte' }}
                                </p>
                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-6 h-6 flex items-center justify-center rounded-full text-outline hover:text-error hover:bg-error-container/50 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </form>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mb-xs text-left">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-[10px] text-outline text-right">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                    
                    <div class="pt-sm border-t border-outline-variant/20 flex gap-sm">
                        <form method="POST" action="{{ route('notifications.markAllAsRead') }}" class="w-full">
                           @csrf @method('PATCH')
                           <button type="submit" class="w-full py-2 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md font-semibold text-center">
                               Tout marquer comme lu
                           </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="material-symbols-outlined text-4xl text-outline-variant mb-sm">notifications_off</span>
                        <p class="font-label-md text-label-md text-on-surface-variant font-semibold">Aucune nouvelle alerte</p>
                        <p class="font-body-sm text-body-sm text-outline mt-xs">Vous êtes à jour !</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Transaction Modal -->
    <div x-show="showTransactionEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showTransactionEditModal = false; selectedTransaction = null" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20">
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-2">Modifier la Transaction</h3>
            <template x-if="selectedTransaction">
                <form :action="'/transactions/' + selectedTransaction.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    @honeypot
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Type</label>
                        <select name="type" x-model="selectedTransaction.type" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                            <option value="revenu">Revenu</option>
                            <option value="depense">Dépense</option>
                            <option value="epargne">Épargne</option>
                            <option value="remboursement">Remboursement reçu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant (FCFA)</label>
                        <input type="number" step="0.01" min="0.01" name="montant" x-model="selectedTransaction.montant" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Catégorie</label>
                        <input type="text" name="categorie" x-model="selectedTransaction.categorie" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Date</label>
                        <input type="date" name="date_transaction" :value="selectedTransaction.date_transaction ? selectedTransaction.date_transaction.substring(0,10) : ''" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Note / Description</label>
                        <input type="text" name="note" x-model="selectedTransaction.note" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                    </div>
                    <div class="flex gap-md pt-md">
                        <button type="button" @click="showTransactionEditModal = false; selectedTransaction = null" class="flex-grow py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
                            Annuler
                        </button>
                        <button type="submit" class="flex-grow py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md shadow-md">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Edit Goal Modal -->
    <div x-show="showGoalEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showGoalEditModal = false; selectedGoal = null" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20">
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-2">Modifier l'Objectif</h3>
            <template x-if="selectedGoal">
                <form :action="'/goals/' + selectedGoal.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    @honeypot
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Nom de l'objectif</label>
                        <input type="text" name="nom" x-model="selectedGoal.nom" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Description</label>
                        <input type="text" name="description" x-model="selectedGoal.description" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant cible (FCFA)</label>
                        <input type="number" step="0.01" min="0.01" name="montant_cible" x-model="selectedGoal.montant_cible" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md" required>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Montant épargné (FCFA)</label>
                        <input type="number" step="0.01" min="0" name="montant_actuel" x-model="selectedGoal.montant_actuel" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Date cible</label>
                        <input type="date" name="date_cible" :value="selectedGoal.date_cible ? selectedGoal.date_cible.substring(0,10) : ''" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md">
                    </div>
                    <div class="flex gap-md pt-md">
                        <button type="button" @click="showGoalEditModal = false; selectedGoal = null" class="flex-grow py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
                            Annuler
                        </button>
                        <button type="submit" class="flex-grow py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md shadow-md">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
