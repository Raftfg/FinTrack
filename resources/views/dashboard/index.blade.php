@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
    <!-- Left Panel (Main Content) -->
    <div class="lg:col-span-2 space-y-lg">
        <!-- Current Balance Card -->
        <section class="bg-primary rounded-xl p-lg text-on-primary shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
            <p class="font-label-md text-label-md uppercase tracking-wider opacity-90 mb-base">Solde actuel</p>
            <div class="flex items-baseline gap-xs mb-lg">
                <h2 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg">{{ number_format($stats['revenus'] - $stats['depenses'] + $stats['epargne'], 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</h2>
            </div>
            <a href="{{ route('transactions.index') }}" class="bg-surface-container-lowest text-primary px-lg py-sm rounded-full font-label-md text-label-md hover:bg-primary-fixed transition-colors active:scale-95 inline-block">
                Voir les détails
            </a>
        </section>

        <!-- Monthly Stats Grid -->
        <section class="grid grid-cols-2 gap-md">
            <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/30">
                <div class="flex items-center gap-xs text-secondary mb-xs">
                    <span class="material-symbols-outlined text-[18px]">arrow_upward</span>
                    <span class="font-label-sm text-label-sm">Revenus</span>
                </div>
                <p class="font-headline-sm text-headline-sm text-secondary font-bold">+{{ number_format($stats['revenus'], 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
            </div>
            <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/30">
                <div class="flex items-center gap-xs text-error mb-xs">
                    <span class="material-symbols-outlined text-[18px]">arrow_downward</span>
                    <span class="font-label-sm text-label-sm">Dépenses</span>
                </div>
                <p class="font-headline-sm text-headline-sm text-tertiary font-bold">-{{ number_format($stats['depenses'], 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
            </div>
        </section>

        <!-- Recent Transactions -->
        <section>
            <div class="flex justify-between items-center mb-md">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Transactions récentes</h3>
                <a href="{{ route('transactions.index') }}" class="font-label-sm text-label-sm text-primary flex items-center gap-xs hover:underline">
                    Voir tout <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="space-y-sm">
                @forelse($stats['dernieresTransactions'] as $transaction)
                <div class="flex items-center justify-between p-md bg-surface-container-lowest rounded-xl hover:bg-surface-container-high transition-colors cursor-pointer group">
                    <div class="flex items-center gap-md">
                        <div class="w-10 h-10 {{ $transaction['is_debit'] ? 'bg-surface-container text-on-surface-variant' : 'bg-secondary-container text-on-secondary-container' }} rounded-full flex items-center justify-center group-hover:bg-primary-container group-hover:text-on-primary-container transition-colors">
                            <span class="material-symbols-outlined">{{ $transaction['is_debit'] ? 'shopping_cart' : 'payments' }}</span>
                        </div>
                        <div>
                            <p class="font-label-md text-label-md font-bold">{{ $transaction['categorie'] }}</p>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $transaction['date_transaction'] }}</p>
                        </div>
                    </div>
                    <p class="font-label-md text-label-md font-bold {{ $transaction['is_debit'] ? 'text-on-surface' : 'text-secondary' }}">
                        {{ $transaction['is_debit'] ? '-' : '+' }}{{ number_format($transaction['montant'], 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}
                    </p>
                </div>
                @empty
                <p class="text-sm text-on-surface-variant">Aucune transaction récente.</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Right Panel (Sidebar Content) -->
    <div class="space-y-lg">
        <!-- Savings & Loans Quick View -->
        <section>
            <h3 class="font-headline-sm text-headline-sm mb-md text-on-surface">Aperçu rapide</h3>
            <div class="flex lg:flex-col gap-md overflow-x-auto lg:overflow-x-visible hide-scrollbar pb-xs lg:pb-0">
                <div class="min-w-[160px] lg:min-w-0 bg-secondary-container/20 p-md rounded-xl border border-secondary/10 flex-grow">
                    <span class="material-symbols-outlined text-secondary mb-base">savings</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Épargne totale</p>
                    <p class="font-headline-sm text-headline-sm font-bold text-on-secondary-container">{{ number_format($stats['epargne'], 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
                </div>
                <div class="min-w-[160px] lg:min-w-0 bg-surface-container-high p-md rounded-xl border border-outline-variant/30 flex-grow">
                    <span class="material-symbols-outlined text-primary mb-base">handshake</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Argent prêté</p>
                    <p class="font-headline-sm text-headline-sm font-bold text-primary">{{ number_format($stats['pretsActifs'], 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
                </div>
                <div class="min-w-[160px] lg:min-w-0 bg-tertiary-fixed p-md rounded-xl border border-tertiary/10 flex-grow">
                    <span class="material-symbols-outlined text-tertiary mb-base">credit_card</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Crédits (TODO)</p>
                    <p class="font-headline-sm text-headline-sm font-bold text-tertiary">0 {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
                </div>
            </div>
        </section>

        <!-- Goals Preview -->
        <section class="bg-surface-container-low p-lg rounded-xl shadow-sm">
            <div class="flex justify-between items-center mb-md">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Objectifs</h3>
                <a href="{{ route('goals.index') }}" class="font-label-sm text-label-sm text-primary hover:underline">Voir tout</a>
            </div>
            
            <div class="space-y-lg">
                @forelse(array_slice($stats['objectifs'], 0, 3) as $objectif)
                <div class="space-y-base">
                    <div class="flex justify-between font-label-md text-label-md text-on-surface-variant mb-xs">
                        <span class="font-semibold text-on-surface">{{ $objectif['nom'] }}</span>
                        <span>{{ $objectif['pct'] }}%</span>
                    </div>
                    <div class="w-full bg-outline-variant/30 h-3 rounded-full overflow-hidden">
                        <div class="bg-secondary h-full rounded-full transition-all duration-1000" style="width: {{ $objectif['pct'] }}%"></div>
                    </div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mt-base">{{ number_format($objectif['montant_actuel'], 0, ',', ' ') }} / {{ number_format($objectif['montant_cible'], 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
                </div>
                @empty
                <p class="text-sm text-on-surface-variant">Aucun objectif en cours.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>


@endsection

@stack('scripts')
<script>
    // Simple micro-interaction for the balance card
    document.addEventListener('DOMContentLoaded', () => {
        const card = document.querySelector('section.bg-primary');
        if(card) {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.background = `radial-gradient(circle at ${x}px ${y}px, #0037c2 0%, #00288e 80%)`;
            });
            card.addEventListener('mouseleave', () => {
                card.style.background = '#00288e';
            });
        }
    });
</script>
