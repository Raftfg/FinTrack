@extends('layouts.app')

@section('title', 'Gestion des Prêts')

@section('content')

{{-- Flash message --}}
@if(session('success'))
<div class="bg-secondary-container text-on-secondary-container px-md py-sm rounded-xl text-label-md mb-md flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    {{ session('success') }}
</div>
@endif

<!-- Header Section -->
<div class="flex justify-between items-end mb-8 mt-4">
    <div>
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-primary">Gestion des Prêts</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Suivez vos remboursements et aides en cours.</p>
    </div>
    <!-- Desktop Add Button -->
    <button @click="showLoanModal = true" class="hidden md:flex items-center gap-2 py-2 px-4 bg-primary text-on-primary rounded-xl hover:bg-primary-fixed hover:text-primary transition-all text-label-md font-semibold active:scale-95 shadow-md">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Nouveau prêt
    </button>
</div>

<!-- Loan Summary Overview (Bento Style) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-lg mb-xl">
    <div class="bg-primary-container p-lg rounded-xl shadow-sm border border-outline-variant/20 text-on-primary-container">
        <p class="opacity-80 font-label-sm text-label-sm mb-2 uppercase tracking-wider">Total dû</p>
        <p class="font-headline-lg-mobile md:font-headline-lg font-bold">{{ number_format($totalDu, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
    </div>
    <div class="bg-surface-container-high p-lg rounded-xl shadow-sm">
        <p class="text-on-surface-variant font-label-sm text-label-sm mb-2 uppercase tracking-wider">Restant à payer</p>
        <p class="text-primary font-headline-lg-mobile md:font-headline-lg font-bold">{{ number_format($totalRestant, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
    </div>
    <div class="bg-secondary-container p-lg rounded-xl shadow-sm text-on-secondary-container">
        <p class="opacity-80 font-label-sm text-label-sm mb-2 uppercase tracking-wider">Taux de remboursement</p>
        <div class="flex items-center gap-3">
            <p class="font-headline-lg-mobile md:font-headline-lg font-bold">{{ $tauxGlobal }}%</p>
            <div class="flex-1 bg-on-secondary-container/20 h-2 rounded-full overflow-hidden">
                <div class="bg-secondary h-full transition-all duration-1000" style="width: {{ $tauxGlobal }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Loans List Section -->
<div class="space-y-md">
    <div class="flex items-center justify-between px-2 mb-2">
        <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Personnes & Engagements</h2>
        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $loans->count() }} prêt(s)</span>
    </div>

    @forelse($loans as $loan)
    @php
        $initials = collect(explode(' ', $loan->personne))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
        $restant = $loan->montant_initial - $loan->montant_rembourse;
        $pct = $loan->montant_initial > 0
            ? min(100, (int)(($loan->montant_rembourse / $loan->montant_initial) * 100))
            : 0;
    @endphp
    <div
        @click="selectedLoan = {{ json_encode(['id' => $loan->id, 'personne' => $loan->personne, 'note' => $loan->note, 'montant_initial' => $loan->montant_initial, 'montant_rembourse' => $loan->montant_rembourse, 'statut' => $loan->statut->value]) }}; showLoanEditModal = true"
        class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-surface-container flex flex-col md:flex-row md:items-center justify-between gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200 group cursor-pointer"
    >
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-surface-container-high flex items-center justify-center text-primary font-bold text-sm">
                {{ $initials }}
            </div>
            <div>
                <p class="font-body-md text-body-md font-semibold text-on-surface">{{ $loan->personne }}</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $loan->note ?? 'Prêt personnel' }}</p>
            </div>
        </div>

        <div class="flex-1 md:max-w-xs">
            <div class="w-full bg-surface-container h-1.5 rounded-full overflow-hidden mb-1">
                <div class="bg-secondary h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $pct }}% remboursé</p>
        </div>

        <div class="grid grid-cols-2 md:flex md:items-center gap-x-8 gap-y-2">
            <div class="md:text-right">
                <p class="font-label-sm text-label-sm text-on-surface-variant">Total dû</p>
                <p class="font-body-md text-body-md font-medium">{{ number_format($loan->montant_initial, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
            </div>
            <div class="md:text-right">
                <p class="font-label-sm text-label-sm text-on-surface-variant">Restant</p>
                <p class="font-body-md text-body-md font-bold
                    {{ $loan->statut->value === 'rembourse' ? 'text-secondary' : ($loan->statut->value === 'non_rembourse' ? 'text-error' : 'text-primary') }}">
                    {{ number_format($restant, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}
                </p>
            </div>
            <div class="col-span-2 md:col-span-1">
                @if($loan->statut->value === 'rembourse')
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-secondary mr-2"></span>Payé
                </span>
                @elseif($loan->statut->value === 'non_rembourse')
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-error mr-2"></span>Non remboursé
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary mr-2"></span>Partiel
                </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">chevron_right</span>
        </div>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <span class="material-symbols-outlined text-6xl text-outline-variant mb-md">account_balance</span>
        <p class="font-headline-sm text-headline-sm text-on-surface-variant font-bold">Aucun prêt enregistré</p>
        <p class="font-body-sm text-body-sm text-outline mt-xs">Cliquez sur « + » pour ajouter votre premier prêt.</p>
        <button @click="showLoanModal = true" class="mt-md flex items-center gap-2 py-2 px-4 bg-primary text-on-primary rounded-xl text-label-md font-semibold active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Nouveau prêt
        </button>
    </div>
    @endforelse
</div>

@endsection
