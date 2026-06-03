@extends('layouts.app')

@section('title', 'Transactions')

@section('content')

{{-- Flash message --}}
@if(session('success'))
<div class="bg-secondary-container text-on-secondary-container px-md py-sm rounded-xl text-label-md mb-md">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-lg">
    <!-- Left Column: Filters and Summaries (1/3 width on desktop) -->
    <div class="lg:col-span-4 space-y-lg">
        <div class="flex flex-col gap-md">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="font-headline-lg-mobile lg:font-headline-lg text-headline-lg-mobile lg:text-headline-lg text-on-surface">Transactions</h1>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Visualisez et triez vos flux financiers.</p>
            </div>
            <button @click="showTransactionModal = true" class="hidden lg:flex items-center gap-2 py-2 px-4 bg-primary text-on-primary rounded-xl hover:bg-primary-fixed hover:text-primary transition-all text-label-md font-semibold active:scale-95 shadow-md">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Nouvelle
            </button>
        </div>
            
            <!-- Export buttons -->
            <div class="flex gap-sm">
                <a href="{{ route('transactions.export.pdf', ['mois' => $mois, 'annee' => $annee, 'type' => $type]) }}" 
                   class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-xl bg-surface-container border border-outline-variant/30 text-on-surface hover:bg-surface-container-high transition-all text-label-md font-semibold active:scale-95"
                   title="Exporter ce mois en PDF">
                    <span class="material-symbols-outlined text-[20px] text-error">picture_as_pdf</span>
                    PDF
                </a>
                <a href="{{ route('transactions.export.excel', ['mois' => $mois, 'annee' => $annee, 'type' => $type]) }}" 
                   class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-xl bg-surface-container border border-outline-variant/30 text-on-surface hover:bg-surface-container-high transition-all text-label-md font-semibold active:scale-95"
                   title="Exporter ce mois en Excel / CSV">
                    <span class="material-symbols-outlined text-[20px] text-secondary">table_view</span>
                    Excel / CSV
                </a>
            </div>
        </div>

        <!-- Month Selector (Horizontal scroll on mobile, wrap on desktop) -->
        <div class="space-y-sm">
            <h3 class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Période</h3>
            <div class="flex lg:flex-wrap gap-sm overflow-x-auto lg:overflow-x-visible pb-2 lg:pb-0 hide-scrollbar">
                @foreach(range(1, 12) as $m)
                @php $monthName = \Carbon\Carbon::create(null, $m)->locale('fr')->isoFormat('MMMM'); @endphp
                <a href="{{ route('transactions.index', ['mois' => $m, 'annee' => $annee, 'type' => $type]) }}"
                   class="flex-shrink-0 px-4 py-2 rounded-full text-label-md font-label-md transition-all text-center lg:flex-grow-0
                   {{ $m == $mois
                       ? 'border-2 border-primary bg-primary-fixed text-primary font-bold'
                       : 'border border-outline-variant text-on-surface-variant hover:border-primary hover:text-primary' }}">
                    {{ ucfirst($monthName) }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Filter by Type (Grid columns changes based on viewport) -->
        <div class="space-y-sm">
            <h3 class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Type de flux</h3>
            <div class="grid grid-cols-3 lg:grid-cols-1 gap-sm">
                @foreach([
                    ['value' => null,         'label' => 'Tout afficher', 'icon' => 'list', 'color' => 'text-on-surface-variant'],
                    ['value' => 'revenu',     'label' => 'Revenus',  'icon' => 'trending_up',   'color' => 'text-secondary'],
                    ['value' => 'depense',    'label' => 'Dépenses', 'icon' => 'trending_down',  'color' => 'text-tertiary'],
                ] as $f)
                <a href="{{ route('transactions.index', ['mois' => $mois, 'annee' => $annee, 'type' => $f['value']]) }}"
                   class="flex items-center justify-center lg:justify-start gap-3 py-3 px-4 rounded-xl border transition-all active:scale-95
                   {{ $type == $f['value']
                       ? 'bg-primary border-primary text-on-primary shadow-md'
                       : 'bg-surface-container-low border-outline-variant/30 hover:shadow-md' }}">
                    <span class="material-symbols-outlined {{ $type == $f['value'] ? 'text-on-primary' : $f['color'] }}">{{ $f['icon'] }}</span>
                    <span class="text-label-md font-label-md">{{ $f['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Month Summary -->
        <div class="space-y-sm">
            <h3 class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Résumé de la période</h3>
            <div class="grid grid-cols-2 lg:grid-cols-1 gap-sm">
                <div class="bg-secondary-container/20 px-md py-sm rounded-xl border border-secondary/10">
                    <p class="text-label-sm text-on-surface-variant">Total Revenus</p>
                    <p class="font-headline-sm text-headline-sm text-secondary font-bold">+{{ number_format($totalRevenus, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-tertiary-fixed/40 px-md py-sm rounded-xl border border-tertiary/10">
                    <p class="text-label-sm text-on-surface-variant">Total Dépenses</p>
                    <p class="font-headline-sm text-headline-sm text-tertiary font-bold">-{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Transactions List (2/3 width on desktop) -->
    <div class="lg:col-span-8 space-y-lg">
        <div class="space-y-xl">
            @forelse($grouped as $date => $transactions)
            @php
                $dateObj   = \Carbon\Carbon::parse($date);
                $dateLabel = $dateObj->isToday()
                    ? "Aujourd'hui, " . $dateObj->locale('fr')->isoFormat('D MMMM')
                    : ($dateObj->isYesterday() ? 'Hier' : $dateObj->locale('fr')->isoFormat('dddd D MMMM'));
                $totalJour = $transactions->sum(fn($t) => $t->type->isDebit() ? -$t->montant : $t->montant);
            @endphp
            <div>
                <div class="flex items-center justify-between mb-md px-2">
                    <h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">{{ ucfirst($dateLabel) }}</h3>
                    <span class="text-label-sm font-label-sm font-bold {{ $totalJour >= 0 ? 'text-secondary' : 'text-outline' }}">
                        {{ $totalJour >= 0 ? '+' : '' }}{{ number_format($totalJour, 2, ',', ' ') }} FCFA
                    </span>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
                    @foreach($transactions as $i => $transaction)
                    @if($i > 0)
                    <div class="mx-md border-t border-outline-variant/10"></div>
                    @endif
                    <div @click="selectedTransaction = {{ json_encode($transaction) }}; showTransactionEditModal = true" class="flex items-center justify-between p-md hover:bg-surface-container-low transition-colors cursor-pointer group">
                        <div class="flex items-center gap-md">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110
                                @if($transaction->type === \App\Domain\Transaction\Enums\TransactionType::REVENU) bg-secondary-container/30 text-secondary
                                @elseif($transaction->type === \App\Domain\Transaction\Enums\TransactionType::EPARGNE) bg-primary-fixed/30 text-primary
                                @elseif($transaction->type === \App\Domain\Transaction\Enums\TransactionType::REMBOURSEMENT) bg-secondary-container text-on-secondary-container
                                @else bg-surface-container text-on-surface-variant
                                @endif">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                                    @if($transaction->type === \App\Domain\Transaction\Enums\TransactionType::REVENU) payments
                                    @elseif($transaction->type === \App\Domain\Transaction\Enums\TransactionType::EPARGNE) savings
                                    @elseif($transaction->type === \App\Domain\Transaction\Enums\TransactionType::REMBOURSEMENT) account_balance_wallet
                                    @elseif($transaction->categorie === 'Alimentation') restaurant
                                    @elseif($transaction->categorie === 'Transport') directions_car
                                    @elseif($transaction->categorie === 'Logement') home
                                    @else shopping_bag
                                    @endif
                                </span>
                            </div>
                            <div>
                                <p class="font-body-md text-body-md font-semibold text-on-surface">
                                    {{ $transaction->note ?? $transaction->categorie }}
                                </p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">
                                    {{ $transaction->type->label() }} • {{ $transaction->categorie }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-sm">
                            <p class="font-headline-sm text-headline-sm font-bold
                                {{ $transaction->type->isDebit() ? 'text-on-surface' : 'text-secondary' }}">
                                {{ $transaction->type->isDebit() ? '-' : '+' }}{{ number_format($transaction->montant, 2, ',', ' ') }} FCFA
                            </p>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction->id) }}" @click.stop>
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full text-outline hover:text-error hover:bg-error-container transition-colors opacity-0 group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <span class="material-symbols-outlined text-6xl text-outline-variant mb-md">receipt_long</span>
                <p class="font-headline-sm text-headline-sm text-on-surface-variant font-bold">Aucune transaction ce mois-ci</p>
                <p class="font-body-sm text-body-sm text-outline mt-xs">Modifiez les filtres de recherche ou ajoutez une transaction.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
