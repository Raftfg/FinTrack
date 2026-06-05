@extends('layouts.app')

@section('title', 'Gestion des Budgets')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="bg-secondary-container text-on-secondary-container px-md py-sm rounded-xl text-label-md mb-md flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-error-container text-on-error-container px-md py-sm rounded-xl text-label-md mb-md flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">error</span>
    {{ session('error') }}
</div>
@endif

@php
    // Calculate previous and next months for navigation chevrons
    $prevMonth = $mois == 1 ? 12 : $mois - 1;
    $prevYear = $mois == 1 ? $annee - 1 : $annee;
    $nextMonth = $mois == 12 ? 1 : $mois + 1;
    $nextYear = $mois == 12 ? $annee + 1 : $annee;

    // Global spent percentage
    $totalPct = $totalBudgetise > 0 ? (int) (($totalDepense / $totalBudgetise) * 100) : 0;
    
    // Dynamic Insight logic
    $exceededCategories = [];
    $nearLimitCategories = [];
    foreach ($budgets as $b) {
        if ($b->pct >= 100) {
            $exceededCategories[] = $b->categorie;
        } elseif ($b->pct >= 80) {
            $nearLimitCategories[] = $b->categorie;
        }
    }

    if (count($exceededCategories) > 0) {
        $insightTitle = "Alerte de dépassement";
        $insightMessage = "Vos dépenses pour '" . implode(', ', $exceededCategories) . "' dépassent le budget défini. Modérez vos dépenses dans ces catégories.";
    } elseif (count($nearLimitCategories) > 0) {
        $insightTitle = "Attention aux limites";
        $insightMessage = "Vos dépenses pour '" . implode(', ', $nearLimitCategories) . "' approchent de la limite (plus de 80%). Soyez vigilants.";
    } else {
        $insightTitle = "Analyse Intelligente";
        if (count($budgets) > 0) {
            $insightMessage = "Félicitations ! Toutes vos catégories budgétaires sont bien maîtrisées ce mois-ci. Continuez ainsi.";
        } else {
            $insightMessage = "Définissez des limites de dépenses mensuelles par catégorie pour commencer à gérer vos budgets.";
        }
    }

    // Category detail mappings
    $iconMap = [
        'Alimentation' => 'restaurant',
        'Transport' => 'directions_car',
        'Santé' => 'medical_services',
        'Logement' => 'home',
        'Éducation' => 'school',
        'Aide Sociale' => 'volunteer_activism',
        'Épargne' => 'savings',
        'Loisirs' => 'celebration',
        'Autre' => 'shopping_bag',
    ];

    // Colors mapping based on category properties
    $colorMap = [
        'Alimentation' => ['bg' => 'bg-secondary-container/20', 'text' => 'text-secondary'],
        'Transport' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary'],
        'Santé' => ['bg' => 'bg-error-container/20', 'text' => 'text-error'],
        'Logement' => ['bg' => 'bg-primary-container/20', 'text' => 'text-primary'],
        'Éducation' => ['bg' => 'bg-surface-container-highest', 'text' => 'text-on-surface'],
        'Aide Sociale' => ['bg' => 'bg-error-container/20', 'text' => 'text-error'],
        'Épargne' => ['bg' => 'bg-primary-fixed/40', 'text' => 'text-primary'],
        'Loisirs' => ['bg' => 'bg-warning-container/20', 'text' => 'text-warning'],
        'Autre' => ['bg' => 'bg-surface-container', 'text' => 'text-on-surface-variant'],
    ];

    // Prep dynamic fields for the add budget modal preview
    $firstAvailable = count($availableCategories) > 0 ? reset($availableCategories) : '';
    $firstIcon = $iconMap[$firstAvailable] ?? 'shopping_bag';
    $firstColor = $colorMap[$firstAvailable] ?? ['bg' => 'bg-surface-container', 'text' => 'text-on-surface-variant'];
@endphp

<div x-data="{
    showAddBudgetModal: false,
    showEditBudgetModal: false,
    selectedBudget: { id: '', categorie: '', montant_limite: '' },
    
    // Add budget form interactive properties
    addBudgetCategory: '{{ $firstAvailable }}',
    addBudgetLimit: '',
    addBudgetIcon: '{{ $firstIcon }}',
    addBudgetIconBg: '{{ $firstColor['bg'] }}',
    addBudgetIconText: '{{ $firstColor['text'] }}',
    addBudgetThreshold: 90,
    enableAlert: true,
    isRecurring: true,

    selectCategory(cat, icon, bg, text) {
        this.addBudgetCategory = cat;
        this.addBudgetIcon = icon;
        this.addBudgetIconBg = bg;
        this.addBudgetIconText = text;
    },

    // Edit budget form properties
    editBudgetLimit: '',
    editBudgetThreshold: 90,
    editEnableAlert: true,
    editIsRecurring: true
}" class="relative">

    <!-- Header & Monthly Selector -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-lg">
        <div>
            <p class="text-primary dark:text-primary-fixed-dim font-label-md text-label-md tracking-wider uppercase">Aperçu</p>
            <h2 class="text-headline-lg-mobile md:text-headline-lg font-headline-lg text-on-surface">Budgets</h2>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto self-start md:self-end">
            <!-- Period Selector Navigation Card -->
            <div class="flex items-center bg-surface-container-low dark:bg-inverse-surface/30 p-1 rounded-xl shadow-sm border border-outline-variant/30 flex-grow md:flex-grow-0 justify-between">
                <a href="{{ route('budgets.index', ['mois' => $prevMonth, 'annee' => $prevYear]) }}" class="p-2 hover:bg-surface-container dark:hover:bg-inverse-surface rounded-lg transition-all text-on-surface flex items-center justify-center" title="Mois Précédent">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </a>
                
                <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center px-2">
                    <select name="mois" onchange="this.form.submit()" class="bg-transparent border-none py-1 pl-2 pr-7 text-label-md font-semibold text-on-surface focus:ring-0 cursor-pointer">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $mois == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->locale('fr')->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                    <select name="annee" onchange="this.form.submit()" class="bg-transparent border-none py-1 pl-1 pr-7 text-label-md font-semibold text-on-surface focus:ring-0 cursor-pointer">
                        @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
                
                <a href="{{ route('budgets.index', ['mois' => $nextMonth, 'annee' => $nextYear]) }}" class="p-2 hover:bg-surface-container dark:hover:bg-inverse-surface rounded-lg transition-all text-on-surface flex items-center justify-center" title="Mois Suivant">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </a>
            </div>

            @if(count($availableCategories) > 0)
            <button @click="showAddBudgetModal = true" class="hidden md:flex items-center justify-center gap-2 py-2.5 px-4 bg-primary text-on-primary rounded-xl hover:bg-primary-container transition-all text-label-md font-semibold active:scale-95 shadow-md">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Définir un budget
            </button>
            @endif
        </div>
    </section>

    <!-- Bento Grid Summary -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-gutter mb-lg">
        <!-- Main Summary Card (Progress SVG & Stats) -->
        <div class="lg:col-span-8 bg-surface-container-lowest dark:bg-inverse-surface/10 rounded-xl p-lg shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-outline-variant/30 flex flex-col md:flex-row items-center gap-lg">
            <!-- Circular Progress Diagram -->
            <div class="relative flex-shrink-0 w-32 h-32 md:w-40 md:h-40">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" fill="transparent" r="45" stroke="currentColor" class="text-outline-variant/20" stroke-width="10"></circle>
                    <circle cx="50" cy="50" fill="transparent" r="45" stroke="currentColor" class="text-primary dark:text-primary-fixed"
                        stroke-dasharray="282.7" 
                        stroke-dashoffset="{{ 282.7 * (1 - (min(100, $totalPct) / 100)) }}" 
                        stroke-linecap="round" stroke-width="10"
                        style="transition: stroke-dashoffset 1.5s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-headline-sm font-headline-sm text-primary dark:text-primary-fixed-dim font-bold">{{ $totalPct }}%</span>
                    <span class="text-label-sm font-label-sm text-on-surface-variant">Dépensé</span>
                </div>
            </div>
            
            <div class="flex-grow space-y-4 w-full text-center md:text-left">
                <div>
                    <h3 class="text-headline-sm font-headline-sm text-on-surface font-semibold">Résumé du Budget Global</h3>
                    <p class="text-body-sm font-body-sm text-on-surface-variant">
                        Vous avez dépensé {{ number_format($totalDepense, 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }} de votre limite mensuelle globale de {{ number_format($totalBudgetise, 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-md pt-4">
                    <div class="p-md rounded-lg bg-surface-container-low dark:bg-inverse-surface/30">
                        <span class="block text-label-sm font-label-sm text-on-surface-variant">Montant alloué</span>
                        <span class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ number_format($totalBudgetise, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                    </div>
                    <div class="p-md rounded-lg bg-surface-container-low dark:bg-inverse-surface/30">
                        <span class="block text-label-sm font-label-sm text-on-surface-variant">Montant dépensé</span>
                        <span class="text-headline-sm font-headline-sm text-primary dark:text-primary-fixed-dim font-bold">{{ number_format($totalDepense, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                    </div>
                    <div class="p-md rounded-lg bg-surface-container-low dark:bg-inverse-surface/30">
                        <span class="block text-label-sm font-label-sm text-on-surface-variant">Montant restant</span>
                        <span class="text-headline-sm font-headline-sm text-secondary font-bold">{{ number_format($totalRestant, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Smart Insight Card -->
        <div class="lg:col-span-4 bg-primary-container text-on-primary-container rounded-xl p-lg shadow-lg flex flex-col justify-between relative overflow-hidden group">
            <div class="relative z-10">
                <span class="material-symbols-outlined text-4xl mb-4" style="font-variation-settings: 'FILL' 1;">lightbulb</span>
                <h3 class="text-headline-sm font-headline-sm font-bold mb-2">{{ $insightTitle }}</h3>
                <p class="text-body-sm font-body-sm opacity-90 leading-relaxed">{{ $insightMessage }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="relative z-10 mt-6 text-label-md font-label-md flex items-center gap-1 hover:underline self-start font-semibold">
                Voir l'analyse <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
            <!-- Decorative element -->
            <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-on-primary-container/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
        </div>
    </section>

    <!-- Budget Categories details list -->
    <section class="space-y-gutter">
        <div class="flex items-center justify-between">
            <h3 class="text-headline-sm font-headline-sm text-on-surface font-bold">Détails des Catégories</h3>
            <span class="text-label-md font-label-md text-on-surface-variant">{{ count($budgets) }} {{ count($budgets) > 1 ? 'Catégories actives' : 'Catégorie active' }}</span>
        </div>

        <div class="grid grid-cols-1 gap-md">
            @forelse($budgets as $budget)
                @php
                    $icon = $iconMap[$budget->categorie] ?? 'shopping_bag';
                    
                    if ($budget->categorie === 'Épargne') {
                        $statusText = 'En bonne voie';
                        $statusIcon = 'trending_up';
                        $textColor = 'text-primary dark:text-primary-fixed-dim';
                        $bgColor = 'bg-primary-fixed/40 dark:bg-primary-container/20';
                        $barColor = 'bg-primary';
                    } elseif ($budget->pct >= 100) {
                        $statusText = 'Budget dépassé';
                        $statusIcon = 'error';
                        $textColor = 'text-error';
                        $bgColor = 'bg-error-container/20';
                        $barColor = 'bg-error';
                    } elseif ($budget->pct >= 80) {
                        $statusText = 'Proche de la limite';
                        $statusIcon = 'warning';
                        $textColor = 'text-tertiary';
                        $bgColor = 'bg-tertiary-fixed/30 dark:bg-tertiary-container/20';
                        $barColor = 'bg-tertiary-container';
                    } else {
                        $statusText = 'En bonne voie';
                        $statusIcon = 'check_circle';
                        $textColor = 'text-secondary';
                        $bgColor = 'bg-secondary-container/20';
                        $barColor = 'bg-secondary';
                    }
                @endphp

                <div class="bg-surface-container-lowest dark:bg-inverse-surface/10 p-md md:p-lg rounded-xl border border-outline-variant/30 shadow-sm flex flex-col md:flex-row md:items-center gap-md hover:shadow-md transition-shadow">
                    <!-- Icon, Category Name, and Status Title -->
                    <div class="flex items-center gap-md md:w-1/4 min-w-[220px]">
                        <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center {{ $bgColor }} {{ $textColor }}">
                            <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">{{ $icon }}</span>
                        </div>
                        <div>
                            <p class="font-headline-sm text-headline-sm text-on-surface font-semibold">{{ $budget->categorie }}</p>
                            <span class="inline-flex items-center gap-1 text-label-sm font-label-sm {{ $textColor }}">
                                <span class="material-symbols-outlined text-[16px]">{{ $statusIcon }}</span>
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    <!-- Progress Bar & Text Details -->
                    <div class="flex-grow space-y-2">
                        <div class="flex justify-between text-label-md font-label-md">
                            <span class="text-on-surface-variant font-medium">
                                {{ number_format($budget->real_spent, 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }} dépensé
                            </span>
                            <span class="text-on-surface font-semibold">
                                sur {{ number_format($budget->montant_limite, 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}
                            </span>
                        </div>
                        <div class="w-full h-3 bg-surface-container-high dark:bg-on-surface/10 rounded-full overflow-hidden">
                            <div class="h-full {{ $barColor }} rounded-full" style="width: {{ min(100, $budget->pct) }}%" data-target-width="{{ min(100, $budget->pct) }}%"></div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 md:pl-md self-end md:self-center">
                        <button @click="selectedBudget = { id: '{{ $budget->id }}', categorie: '{{ $budget->categorie }}', montant_limite: '{{ $budget->montant_limite }}', real_spent: '{{ $budget->real_spent }}' }; editBudgetLimit = '{{ $budget->montant_limite }}'; showEditBudgetModal = true" class="py-2 px-3 font-label-md text-label-md text-primary border border-primary/20 rounded-lg hover:bg-primary/5 transition-colors font-semibold flex items-center gap-1 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">edit</span> Modifier
                        </button>
                        <form method="POST" action="{{ route('budgets.destroy', $budget->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer ce budget ?')" class="p-2 border border-error/20 text-error rounded-lg hover:bg-error-container/20 transition-colors flex items-center justify-center cursor-pointer" title="Supprimer">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-20 text-center bg-surface-container-lowest dark:bg-inverse-surface/10 rounded-xl border border-outline-variant/30">
                    <span class="material-symbols-outlined text-6xl text-outline-variant mb-md">shopping_bag</span>
                    <p class="font-headline-sm text-headline-sm text-on-surface-variant font-bold">Aucun budget défini</p>
                    <p class="font-body-sm text-body-sm text-outline mt-xs">Vous n'avez pas encore défini de budget pour ce mois.</p>
                    @if(count($availableCategories) > 0)
                    <button @click="showAddBudgetModal = true" class="mt-md flex items-center gap-2 py-2.5 px-5 bg-primary text-on-primary rounded-xl text-label-md font-semibold active:scale-95 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Définir un budget
                    </button>
                    @endif
                </div>
            @endforelse
        </div>
    </section>

    <!-- FAB for quick action (Mobile only) -->
    @if(count($availableCategories) > 0)
    <button @click="showAddBudgetModal = true" class="fixed right-6 bottom-24 w-14 h-14 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-40 group md:hidden cursor-pointer">
        <span class="material-symbols-outlined text-3xl">add</span>
        <span class="absolute right-full mr-4 bg-inverse-surface text-inverse-on-surface px-3 py-1 rounded-lg text-label-md opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
            Définir un budget
        </span>
    </button>
    @endif

    <!-- Add Budget Modal ("Configurer un Budget") -->
    <div x-show="showAddBudgetModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showAddBudgetModal = false" class="bg-surface-container-lowest dark:bg-inverse-surface text-on-surface w-full max-w-4xl rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20" style="max-height: 90vh; overflow-y: auto;">
            
            <header class="flex justify-between items-center pb-sm border-b border-outline-variant/20">
                <div class="flex items-center gap-md">
                    <button type="button" @click="showAddBudgetModal = false" class="hover:bg-surface-container-high dark:hover:bg-on-surface/10 p-2 rounded-full transition-colors duration-200 cursor-pointer">
                        <span class="material-symbols-outlined text-primary dark:text-primary-fixed-dim">arrow_back</span>
                    </button>
                    <h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim">Configurer un Budget</h1>
                </div>
                <button type="button" @click="showAddBudgetModal = false" class="hover:bg-surface-container-high dark:hover:bg-on-surface/10 p-2 rounded-full transition-colors duration-200 cursor-pointer">
                    <span class="material-symbols-outlined text-outline">close</span>
                </button>
            </header>

            <div class="flex flex-col lg:flex-row gap-lg pt-sm w-full">
                <!-- Form Section -->
                <div class="w-full lg:flex-grow space-y-xl">
                    <form method="POST" action="{{ route('budgets.store') }}" class="space-y-xl">
                        @csrf
                        @honeypot
                        <input type="hidden" name="mois" value="{{ $mois }}"/>
                        <input type="hidden" name="annee" value="{{ $annee }}"/>
                        <input type="hidden" name="categorie" :value="addBudgetCategory" required/>

                        <!-- Category Selector -->
                        <section class="space-y-md">
                            <label class="block font-label-md text-label-md text-on-surface-variant">Catégorie du budget</label>
                            <div class="flex flex-wrap gap-sm">
                                @forelse($availableCategories as $category)
                                    @php
                                        $cIcon = $iconMap[$category] ?? 'shopping_bag';
                                        $cBg = $colorMap[$category]['bg'] ?? 'bg-surface-container';
                                        $cText = $colorMap[$category]['text'] ?? 'text-on-surface-variant';
                                    @endphp
                                    <button type="button" @click="selectCategory('{{ $category }}', '{{ $cIcon }}', '{{ $cBg }}', '{{ $cText }}')" 
                                        class="flex items-center gap-xs px-md py-sm rounded-full transition-all duration-200 border-2 active:scale-95 text-label-md font-label-md cursor-pointer"
                                        :class="addBudgetCategory === '{{ $category }}' ? 'bg-secondary-container text-on-secondary-container border-transparent' : 'bg-surface-container-high dark:bg-on-surface/10 text-on-surface-variant border-transparent hover:bg-surface-container-highest'">
                                        <span class="material-symbols-outlined text-[18px]">{{ $cIcon }}</span>
                                        <span>{{ $category }}</span>
                                    </button>
                                @empty
                                    <p class="text-body-sm text-outline">Toutes les catégories sont déjà configurées ce mois-ci.</p>
                                @endforelse
                            </div>
                        </section>

                        <!-- Limit Input -->
                        <section class="space-y-md">
                            <label class="block font-label-md text-label-md text-on-surface-variant">Limite mensuelle ({{ config('fintrack.currency_symbol', 'FCFA') }})</label>
                            <div class="relative group">
                                <div class="absolute left-md top-1/2 -translate-y-1/2 text-on-surface-variant">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>
                                <input type="number" step="0.01" min="0.01" name="montant_limite" x-model="addBudgetLimit" 
                                    class="w-full pl-12 pr-md py-md bg-surface-container dark:bg-on-surface/10 border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-headline-sm font-headline-sm text-on-surface" 
                                    placeholder="0.00" required>
                            </div>
                        </section>

                        <!-- Alert Threshold Selection -->
                        <section class="space-y-lg bg-surface-container-low dark:bg-inverse-surface/30 p-lg rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-headline-sm text-[16px] text-on-surface font-semibold">Alerte de seuil</h3>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Recevoir une notification avant d'atteindre la limite.</p>
                                </div>
                                <button type="button" @click="enableAlert = !enableAlert" 
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none cursor-pointer" 
                                    :class="enableAlert ? 'bg-primary' : 'bg-outline-variant'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200" 
                                        :class="enableAlert ? 'translate-x-6' : 'translate-x-1'"></span>
                                </button>
                            </div>
                            <div x-show="enableAlert" x-collapse class="flex gap-md">
                                <button type="button" @click="addBudgetThreshold = 80" 
                                    class="threshold-btn flex-1 py-sm px-md rounded-lg font-label-md text-label-md transition-colors cursor-pointer text-center"
                                    :class="addBudgetThreshold === 80 ? 'border-2 border-primary bg-primary-fixed text-on-primary-fixed font-bold' : 'border border-outline-variant bg-surface-container-lowest dark:bg-inverse-surface text-on-surface-variant hover:border-primary'">
                                    80%
                                </button>
                                <button type="button" @click="addBudgetThreshold = 90" 
                                    class="threshold-btn flex-1 py-sm px-md rounded-lg font-label-md text-label-md transition-colors cursor-pointer text-center"
                                    :class="addBudgetThreshold === 90 ? 'border-2 border-primary bg-primary-fixed text-on-primary-fixed font-bold' : 'border border-outline-variant bg-surface-container-lowest dark:bg-inverse-surface text-on-surface-variant hover:border-primary'">
                                    90%
                                </button>
                                <button type="button" @click="addBudgetThreshold = 100" 
                                    class="threshold-btn flex-1 py-sm px-md rounded-lg font-label-md text-label-md transition-colors cursor-pointer text-center"
                                    :class="addBudgetThreshold === 100 ? 'border-2 border-primary bg-primary-fixed text-on-primary-fixed font-bold' : 'border border-outline-variant bg-surface-container-lowest dark:bg-inverse-surface text-on-surface-variant hover:border-primary'">
                                    100%
                                </button>
                            </div>
                            <div class="pt-md border-t border-outline-variant/30 flex items-center justify-between">
                                <div>
                                    <h3 class="font-headline-sm text-[16px] text-on-surface font-semibold">Récurrence</h3>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Réinitialiser le budget automatiquement chaque mois.</p>
                                </div>
                                <button type="button" @click="isRecurring = !isRecurring" 
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none cursor-pointer" 
                                    :class="isRecurring ? 'bg-primary' : 'bg-outline-variant'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200" 
                                        :class="isRecurring ? 'translate-x-6' : 'translate-x-1'"></span>
                                </button>
                            </div>
                        </section>

                        <!-- Main CTA -->
                        <button type="submit" class="w-full bg-primary text-on-primary py-lg rounded-xl font-headline-sm text-headline-sm shadow-md hover:bg-primary-container transition-all active:scale-[0.98] cursor-pointer font-bold">
                            Enregistrer le budget
                        </button>
                    </form>
                </div>

                <!-- Preview Column (Desktop only) -->
                <div class="hidden lg:block w-96 flex-shrink-0 space-y-md">
                    <h4 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider pl-sm">Aperçu du budget</h4>
                    <div class="w-full p-lg rounded-2xl bg-white dark:bg-inverse-surface/30 shadow-sm border border-outline-variant/20 space-y-xl sticky top-24 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-md">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-200" :class="addBudgetIconBg + ' ' + addBudgetIconText">
                                    <span class="material-symbols-outlined text-[28px]" x-text="addBudgetIcon"></span>
                                </div>
                                <div>
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold" x-text="addBudgetCategory || 'Catégorie'"></h4>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Mensuel • Actif</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-label-sm text-label-sm text-on-surface-variant">Disponible</p>
                                <p class="font-headline-md text-headline-md text-primary dark:text-primary-fixed-dim font-bold" x-text="(parseFloat(addBudgetLimit) || 0).toFixed(2) + ' {{ config('fintrack.currency_symbol', 'FCFA') }}'"></p>
                            </div>
                        </div>
                        
                        <!-- Progress Bar Preview -->
                        <div class="space-y-sm">
                            <div class="flex justify-between font-label-md text-label-md">
                                <span class="text-on-surface-variant">Dépensé: 0.00 {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                                <span class="text-on-surface-variant">0%</span>
                            </div>
                            <div class="h-3 w-full bg-surface-container-high dark:bg-on-surface/10 rounded-full overflow-hidden">
                                <div class="h-full w-[2%] bg-secondary rounded-full"></div>
                            </div>
                        </div>

                        <!-- Alert Threshold text info -->
                        <div x-show="enableAlert && addBudgetLimit" class="flex gap-sm items-center p-md bg-surface-container-low dark:bg-inverse-surface/40 rounded-lg border border-primary/10 transition-all">
                            <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant leading-tight">
                                Vous serez alerté lorsque vos dépenses atteindront <span class="font-bold text-primary dark:text-primary-fixed-dim" x-text="((parseFloat(addBudgetLimit) || 0) * addBudgetThreshold / 100).toFixed(2) + ' {{ config('fintrack.currency_symbol', 'FCFA') }}'"></span> (<span x-text="addBudgetThreshold"></span>%).
                            </p>
                        </div>
                    </div>

                    <!-- Decorative illustration card -->
                    <div class="relative h-44 rounded-2xl overflow-hidden shadow-lg border border-outline-variant/10">
                        <img alt="Financial Planning" class="w-full h-full object-cover brightness-[0.85] dark:brightness-[0.7]" src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=600&auto=format&fit=crop"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-lg">
                            <p class="text-white font-body-sm text-body-sm">Planifiez votre avenir sereinement avec FinTrack.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Budget Modal -->
    <div x-show="showEditBudgetModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showEditBudgetModal = false; selectedBudget = { id: '', categorie: '', montant_limite: '' }" class="bg-surface-container-lowest dark:bg-inverse-surface text-on-surface w-full max-w-4xl rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20" style="max-height: 90vh; overflow-y: auto;">
            
            <header class="flex justify-between items-center pb-sm border-b border-outline-variant/20">
                <div class="flex items-center gap-md">
                    <button type="button" @click="showEditBudgetModal = false; selectedBudget = { id: '', categorie: '', montant_limite: '' }" class="hover:bg-surface-container-high dark:hover:bg-on-surface/10 p-2 rounded-full transition-colors duration-200 cursor-pointer">
                        <span class="material-symbols-outlined text-primary dark:text-primary-fixed-dim">arrow_back</span>
                    </button>
                    <h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim">Modifier le Budget</h1>
                </div>
                <button type="button" @click="showEditBudgetModal = false; selectedBudget = { id: '', categorie: '', montant_limite: '' }" class="hover:bg-surface-container-high dark:hover:bg-on-surface/10 p-2 rounded-full transition-colors duration-200 cursor-pointer">
                    <span class="material-symbols-outlined text-outline">close</span>
                </button>
            </header>

            <template x-if="selectedBudget.id">
                <div class="flex flex-col lg:flex-row gap-lg pt-sm w-full">
                    <!-- Form Section -->
                    <div class="w-full lg:flex-grow space-y-xl">
                        <form :action="'/budgets/' + selectedBudget.id" method="POST" class="space-y-xl">
                            @csrf
                            @method('PATCH')
                            @honeypot
                            
                            <!-- Category display -->
                            <section class="space-y-md">
                                <label class="block font-label-md text-label-md text-on-surface-variant">Catégorie du budget</label>
                                <div class="flex items-center gap-sm">
                                    <div class="flex items-center gap-xs px-md py-sm bg-secondary-container/20 text-secondary border-2 border-secondary/20 rounded-full">
                                        <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                                        <span class="font-label-md text-label-md font-bold" x-text="selectedBudget.categorie"></span>
                                    </div>
                                    <span class="text-body-sm text-outline-variant">(Non modifiable)</span>
                                </div>
                            </section>

                            <!-- Limit Input -->
                            <section class="space-y-md">
                                <label class="block font-label-md text-label-md text-on-surface-variant">Limite mensuelle ({{ config('fintrack.currency_symbol', 'FCFA') }})</label>
                                <div class="relative group">
                                    <div class="absolute left-md top-1/2 -translate-y-1/2 text-on-surface-variant">
                                        <span class="material-symbols-outlined">payments</span>
                                    </div>
                                    <input type="number" step="0.01" min="0.01" name="montant_limite" x-model="editBudgetLimit" 
                                        class="w-full pl-12 pr-md py-md bg-surface-container dark:bg-on-surface/10 border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-headline-sm font-headline-sm text-on-surface" 
                                        required>
                                </div>
                            </section>

                            <!-- Toggles and Alert Settings -->
                            <section class="space-y-lg bg-surface-container-low dark:bg-inverse-surface/30 p-lg rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-headline-sm text-[16px] text-on-surface font-semibold">Alerte de seuil</h3>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">Recevoir une notification avant d'atteindre la limite.</p>
                                    </div>
                                    <button type="button" @click="editEnableAlert = !editEnableAlert" 
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none cursor-pointer" 
                                        :class="editEnableAlert ? 'bg-primary' : 'bg-outline-variant'">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200" 
                                            :class="editEnableAlert ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                </div>
                                <div x-show="editEnableAlert" x-collapse class="flex gap-md">
                                    <button type="button" @click="editBudgetThreshold = 80" 
                                        class="threshold-btn flex-1 py-sm px-md rounded-lg font-label-md text-label-md transition-colors cursor-pointer text-center"
                                        :class="editBudgetThreshold === 80 ? 'border-2 border-primary bg-primary-fixed text-on-primary-fixed font-bold' : 'border border-outline-variant bg-surface-container-lowest dark:bg-inverse-surface text-on-surface-variant hover:border-primary'">
                                        80%
                                    </button>
                                    <button type="button" @click="editBudgetThreshold = 90" 
                                        class="threshold-btn flex-1 py-sm px-md rounded-lg font-label-md text-label-md transition-colors cursor-pointer text-center"
                                        :class="editBudgetThreshold === 90 ? 'border-2 border-primary bg-primary-fixed text-on-primary-fixed font-bold' : 'border border-outline-variant bg-surface-container-lowest dark:bg-inverse-surface text-on-surface-variant hover:border-primary'">
                                        90%
                                    </button>
                                    <button type="button" @click="editBudgetThreshold = 100" 
                                        class="threshold-btn flex-1 py-sm px-md rounded-lg font-label-md text-label-md transition-colors cursor-pointer text-center"
                                        :class="editBudgetThreshold === 100 ? 'border-2 border-primary bg-primary-fixed text-on-primary-fixed font-bold' : 'border border-outline-variant bg-surface-container-lowest dark:bg-inverse-surface text-on-surface-variant hover:border-primary'">
                                        100%
                                    </button>
                                </div>
                                <div class="pt-md border-t border-outline-variant/30 flex items-center justify-between">
                                    <div>
                                        <h3 class="font-headline-sm text-[16px] text-on-surface font-semibold">Récurrence</h3>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">Réinitialiser le budget automatiquement chaque mois.</p>
                                    </div>
                                    <button type="button" @click="editIsRecurring = !editIsRecurring" 
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none cursor-pointer" 
                                        :class="editIsRecurring ? 'bg-primary' : 'bg-outline-variant'">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200" 
                                            :class="editIsRecurring ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                </div>
                            </section>

                            <!-- CTA -->
                            <button type="submit" class="w-full bg-primary text-on-primary py-lg rounded-xl font-headline-sm text-headline-sm shadow-md hover:bg-primary-container transition-all active:scale-[0.98] cursor-pointer font-bold">
                                Enregistrer les modifications
                            </button>
                        </form>
                    </div>

                    <!-- Preview Column (Desktop only) -->
                    <div class="hidden lg:block w-96 flex-shrink-0 space-y-md">
                        <h4 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider pl-sm">Aperçu du budget</h4>
                        <div class="w-full p-lg rounded-2xl bg-white dark:bg-inverse-surface/30 shadow-sm border border-outline-variant/20 space-y-xl sticky top-24 transition-all duration-300">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-md">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-200" 
                                        :class="selectedBudget.categorie === 'Alimentation' ? 'bg-secondary-container/20 text-secondary' : (selectedBudget.categorie === 'Transport' ? 'bg-tertiary-fixed/30 text-tertiary' : (selectedBudget.categorie === 'Santé' ? 'bg-error-container/20 text-error' : (selectedBudget.categorie === 'Logement' ? 'bg-primary-container/20 text-primary' : (selectedBudget.categorie === 'Aide Sociale' ? 'bg-error-container/20 text-error' : (selectedBudget.categorie === 'Épargne' ? 'bg-primary-fixed/40 text-primary' : (selectedBudget.categorie === 'Loisirs' ? 'bg-warning-container/20 text-warning' : 'bg-surface-container text-on-surface-variant'))))))">
                                        <span class="material-symbols-outlined text-[28px]" 
                                            x-text="selectedBudget.categorie === 'Alimentation' ? 'restaurant' : (selectedBudget.categorie === 'Transport' ? 'directions_car' : (selectedBudget.categorie === 'Santé' ? 'medical_services' : (selectedBudget.categorie === 'Logement' ? 'home' : (selectedBudget.categorie === 'Éducation' ? 'school' : (selectedBudget.categorie === 'Aide Sociale' ? 'volunteer_activism' : (selectedBudget.categorie === 'Épargne' ? 'savings' : (selectedBudget.categorie === 'Loisirs' ? 'celebration' : 'shopping_bag')))))))"></span>
                                    </div>
                                    <div>
                                        <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold" x-text="selectedBudget.categorie"></h4>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">Mensuel • Actif</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-label-sm text-label-sm text-on-surface-variant">Disponible</p>
                                    <p class="font-headline-md text-headline-md text-primary dark:text-primary-fixed-dim font-bold" x-text="(parseFloat(editBudgetLimit) || 0).toFixed(2) + ' {{ config('fintrack.currency_symbol', 'FCFA') }}'"></p>
                                </div>
                            </div>
                            
                            <!-- Progress Bar Preview -->
                            <div class="space-y-sm">
                                <div class="flex justify-between font-label-md text-label-md">
                                    <span class="text-on-surface-variant" x-text="'Dépensé: ' + (parseFloat(selectedBudget.real_spent) || 0).toFixed(2) + ' {{ config('fintrack.currency_symbol', 'FCFA') }}'"></span>
                                    <span class="text-on-surface-variant" x-text="(parseFloat(editBudgetLimit) > 0 ? Math.min(100, Math.round((parseFloat(selectedBudget.real_spent) || 0) / parseFloat(editBudgetLimit) * 100)) : 0) + '%'"></span>
                                </div>
                                <div class="h-3 w-full bg-surface-container-high dark:bg-on-surface/10 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300"
                                        :style="'width: ' + (parseFloat(editBudgetLimit) > 0 ? Math.min(100, Math.round((parseFloat(selectedBudget.real_spent) || 0) / parseFloat(editBudgetLimit) * 100)) : 0) + '%'"
                                        :class="(parseFloat(editBudgetLimit) > 0 && ((parseFloat(selectedBudget.real_spent) || 0) / parseFloat(editBudgetLimit) * 100) >= 100) ? 'bg-error' : (((parseFloat(editBudgetLimit) > 0 && ((parseFloat(selectedBudget.real_spent) || 0) / parseFloat(editBudgetLimit) * 100) >= 80)) ? 'bg-tertiary-container' : 'bg-secondary')"></div>
                                </div>
                            </div>

                            <!-- Alert Threshold text info -->
                            <div x-show="editEnableAlert && editBudgetLimit" class="flex gap-sm items-center p-md rounded-lg border transition-all"
                                :class="(parseFloat(selectedBudget.real_spent) || 0) >= ((parseFloat(editBudgetLimit) || 0) * editBudgetThreshold / 100)
                                    ? ((parseFloat(selectedBudget.real_spent) || 0) >= (parseFloat(editBudgetLimit) || 0)
                                        ? 'bg-error-container/20 border-error/20 text-error'
                                        : 'bg-tertiary-fixed/20 border-tertiary/20 text-tertiary')
                                    : 'bg-surface-container-low dark:bg-inverse-surface/40 border-primary/10 text-on-surface-variant'">
                                <span class="material-symbols-outlined text-[20px]"
                                    :class="(parseFloat(selectedBudget.real_spent) || 0) >= ((parseFloat(editBudgetLimit) || 0) * editBudgetThreshold / 100)
                                        ? ((parseFloat(selectedBudget.real_spent) || 0) >= (parseFloat(editBudgetLimit) || 0)
                                            ? 'text-error'
                                            : 'text-tertiary')
                                        : 'text-primary'"
                                    x-text="(parseFloat(selectedBudget.real_spent) || 0) >= ((parseFloat(editBudgetLimit) || 0) * editBudgetThreshold / 100)
                                        ? ((parseFloat(selectedBudget.real_spent) || 0) >= (parseFloat(editBudgetLimit) || 0)
                                            ? 'error'
                                            : 'warning')
                                        : 'info'"></span>
                                <p class="font-body-sm text-body-sm leading-tight flex-grow">
                                    <template x-if="(parseFloat(selectedBudget.real_spent) || 0) >= (parseFloat(editBudgetLimit) || 0)">
                                        <span>Budget entièrement dépassé ! Limite de <span class="font-bold" x-text="(parseFloat(editBudgetLimit) || 0).toFixed(2) + ' {{ config('fintrack.currency_symbol', 'FCFA') }}'"></span> atteinte.</span>
                                    </template>
                                    <template x-if="(parseFloat(selectedBudget.real_spent) || 0) >= ((parseFloat(editBudgetLimit) || 0) * editBudgetThreshold / 100) && (parseFloat(selectedBudget.real_spent) || 0) < (parseFloat(editBudgetLimit) || 0)">
                                        <span>Seuil d'alerte de <span class="font-bold" x-text="((parseFloat(editBudgetLimit) || 0) * editBudgetThreshold / 100).toFixed(2) + ' {{ config('fintrack.currency_symbol', 'FCFA') }}'"></span> (<span x-text="editBudgetThreshold"></span>%) dépassé !</span>
                                    </template>
                                    <template x-if="(parseFloat(selectedBudget.real_spent) || 0) < ((parseFloat(editBudgetLimit) || 0) * editBudgetThreshold / 100)">
                                        <span>Vous serez alerté lorsque vos dépenses atteindront <span class="font-bold text-primary dark:text-primary-fixed-dim" x-text="((parseFloat(editBudgetLimit) || 0) * editBudgetThreshold / 100).toFixed(2) + ' {{ config('fintrack.currency_symbol', 'FCFA') }}'"></span> (<span x-text="editBudgetThreshold"></span>%).</span>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Dynamic transition-in for progress bars
    document.querySelectorAll('[data-target-width]').forEach(bar => {
        const targetWidth = bar.getAttribute('data-target-width');
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 1.5s cubic-bezier(0.22, 1, 0.36, 1)';
            bar.style.width = targetWidth;
        }, 100);
    });
});
</script>
@endpush
