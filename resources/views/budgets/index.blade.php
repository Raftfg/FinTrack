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

<div x-data="{
    showAddBudgetModal: false,
    showEditBudgetModal: false,
    selectedBudget: { id: '', categorie: '', montant_limite: '' }
}" class="relative">

    <!-- Header Section with Month/Year Filters -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-lg gap-4">
        <div>
            <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-xs font-semibold">Mes Budgets</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Fixez et suivez vos limites de dépenses par catégorie.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Period Selector Form -->
            <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-2 bg-surface-container-low p-xs rounded-xl border border-outline-variant/30">
                <select name="mois" onchange="this.form.submit()" class="bg-transparent border-none py-1.5 pl-3 pr-8 text-label-md font-semibold text-on-surface focus:ring-0">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $mois == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->locale('fr')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
                <select name="annee" onchange="this.form.submit()" class="bg-transparent border-none py-1.5 pl-3 pr-8 text-label-md font-semibold text-on-surface focus:ring-0">
                    @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>

            @if(count($availableCategories) > 0)
            <button @click="showAddBudgetModal = true" class="flex-grow md:flex-grow-0 flex items-center justify-center gap-2 py-2.5 px-4 bg-primary text-on-primary rounded-xl hover:bg-primary-fixed hover:text-primary transition-all text-label-md font-semibold active:scale-95 shadow-md">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Définir un budget
            </button>
            @endif
        </div>
    </div>

    <!-- Bento Grid Summary Stats -->
    <section class="grid grid-cols-1 md:grid-cols-4 gap-md mb-lg">
        <div class="bg-surface-container-low p-md rounded-xl shadow-sm border border-outline-variant/30">
            <div class="flex items-center gap-xs text-primary mb-xs">
                <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                <span class="font-label-sm text-label-sm">Total Budgétisé</span>
            </div>
            <p class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ number_format($totalBudgetise, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
        </div>
        <div class="bg-surface-container-low p-md rounded-xl shadow-sm border border-outline-variant/30">
            <div class="flex items-center gap-xs text-tertiary mb-xs">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                <span class="font-label-sm text-label-sm">Dépenses Réelles</span>
            </div>
            <p class="font-headline-sm text-headline-sm font-bold text-tertiary">{{ number_format($totalDepense, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
        </div>
        <div class="bg-surface-container-low p-md rounded-xl shadow-sm border border-outline-variant/30">
            <div class="flex items-center gap-xs text-secondary mb-xs">
                <span class="material-symbols-outlined text-[18px]">savings</span>
                <span class="font-label-sm text-label-sm">Reste Global</span>
            </div>
            <p class="font-headline-sm text-headline-sm font-bold text-secondary">{{ number_format($totalRestant, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</p>
        </div>
        <div class="p-md rounded-xl shadow-sm border border-outline-variant/30 {{ $depassementsCount > 0 ? 'bg-error-container/35 border-error/20 text-error' : 'bg-surface-container-low text-on-surface-variant' }}">
            <div class="flex items-center gap-xs mb-xs">
                <span class="material-symbols-outlined text-[18px]">notification_important</span>
                <span class="font-label-sm text-label-sm">Dépassé(s)</span>
            </div>
            <p class="font-headline-sm text-headline-sm font-bold {{ $depassementsCount > 0 ? 'text-error' : 'text-on-surface' }}">{{ $depassementsCount }} budget(s)</p>
        </div>
    </section>

    <!-- Budgets List -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-lg">
        @forelse($budgets as $budget)
        @php
            // Assign icons dynamically based on category name
            $icon = 'shopping_bag';
            if ($budget->categorie === 'Alimentation') $icon = 'restaurant';
            elseif ($budget->categorie === 'Transport') $icon = 'directions_car';
            elseif ($budget->categorie === 'Santé') $icon = 'medical_services';
            elseif ($budget->categorie === 'Logement') $icon = 'home';
            elseif ($budget->categorie === 'Éducation') $icon = 'school';
            elseif ($budget->categorie === 'Aide Sociale') $icon = 'volunteer_activism';
            elseif ($budget->categorie === 'Épargne') $icon = 'savings';
            elseif ($budget->categorie === 'Loisirs') $icon = 'sports_esports';

            // Determine colors based on thresholds
            $barColor = 'bg-primary';
            $textColor = 'text-primary';
            $bgColor = 'bg-primary-container/20';

            if ($budget->pct >= 100) {
                $barColor = 'bg-error';
                $textColor = 'text-error';
                $bgColor = 'bg-error-container/20';
            } elseif ($budget->pct >= 80) {
                $barColor = 'bg-warning';
                $textColor = 'text-warning';
                $bgColor = 'bg-warning-container/20';
            } else {
                $barColor = 'bg-secondary';
                $textColor = 'text-secondary';
                $bgColor = 'bg-secondary-container/20';
            }
        @endphp
        <div class="col-span-1 md:col-span-4 bg-surface-container-lowest p-lg rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <!-- Top details -->
                <div class="flex justify-between items-start mb-md">
                    <div class="p-sm rounded-lg {{ $bgColor }} {{ $textColor }}">
                        <span class="material-symbols-outlined block text-[22px]">{{ $icon }}</span>
                    </div>
                    <span class="font-label-md text-label-md px-2.5 py-1 font-bold rounded-full {{ $bgColor }} {{ $textColor }}">
                        {{ $budget->pct }}%
                    </span>
                </div>

                <h3 class="font-headline-sm text-headline-sm text-on-surface mb-base font-semibold">{{ $budget->categorie }}</h3>

                <!-- Expense vs Limit Details -->
                <div class="space-y-sm mb-lg">
                    <div class="flex justify-between text-body-sm">
                        <span class="text-on-surface-variant">Dépensé</span>
                        <span class="font-semibold text-on-surface">{{ number_format($budget->real_spent, 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                    </div>
                    <div class="flex justify-between text-body-sm">
                        <span class="text-on-surface-variant">Limite</span>
                        <span class="font-semibold text-on-surface">{{ number_format($budget->montant_limite, 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                    </div>
                    <div class="flex justify-between text-body-sm border-t border-outline-variant/20 pt-xs">
                        <span class="text-on-surface-variant">{{ $budget->is_over ? 'Dépassement' : 'Restant' }}</span>
                        <span class="font-bold {{ $budget->is_over ? 'text-error' : 'text-secondary' }}">
                            {{ number_format($budget->is_over ? ($budget->real_spent - $budget->montant_limite) : $budget->remaining, 2, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <!-- Progress bar -->
                <div class="w-full bg-outline-variant/20 h-2.5 rounded-full mb-md overflow-hidden">
                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-1000" style="width: {{ min(100, $budget->pct) }}%"></div>
                </div>

                <!-- Action buttons -->
                <div class="flex gap-sm pt-xs">
                    <button @click="selectedBudget = { id: '{{ $budget->id }}', categorie: '{{ $budget->categorie }}', montant_limite: '{{ $budget->montant_limite }}' }; showEditBudgetModal = true" class="flex-1 py-2 font-label-md text-label-md text-primary border border-primary/30 rounded-lg hover:bg-primary/5 transition-colors font-semibold flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">edit</span> Modifier
                    </button>
                    <form method="POST" action="{{ route('budgets.destroy', $budget->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Supprimer ce budget ?')" class="p-2 border border-error-container text-error rounded-lg hover:bg-error-container transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-span-1 md:col-span-12 flex flex-col items-center justify-center py-20 text-center">
            <span class="material-symbols-outlined text-6xl text-outline-variant mb-md">shopping_bag</span>
            <p class="font-headline-sm text-headline-sm text-on-surface-variant font-bold">Aucun budget défini</p>
            <p class="font-body-sm text-body-sm text-outline mt-xs">Vous n'avez pas encore défini de budget pour ce mois.</p>
            @if(count($availableCategories) > 0)
            <button @click="showAddBudgetModal = true" class="mt-md flex items-center gap-2 py-2.5 px-5 bg-primary text-on-primary rounded-xl text-label-md font-semibold active:scale-95 transition-transform shadow-md shadow-primary/10">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Définir un budget
            </button>
            @endif
        </div>
        @endforelse
    </div>

    <!-- Add Budget Modal -->
    <div x-show="showAddBudgetModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showAddBudgetModal = false" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20" style="max-height: 85vh; overflow-y: auto;">
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-2">Définir un Budget</h3>
            <form method="POST" action="{{ route('budgets.store') }}" class="space-y-4">
                @csrf
                @honeypot
                <input type="hidden" name="mois" value="{{ $mois }}"/>
                <input type="hidden" name="annee" value="{{ $annee }}"/>

                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Catégorie <span class="text-red-500 font-bold">*</span></label>
                    <select name="categorie" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary" required>
                        @foreach($availableCategories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Limite mensuelle (FCFA) <span class="text-red-500 font-bold">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="montant_limite" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary" required placeholder="ex: 150000">
                </div>

                <div class="flex gap-md pt-md">
                    <button type="button" @click="showAddBudgetModal = false" class="flex-grow py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
                        Annuler
                    </button>
                    <button type="submit" class="flex-grow py-3 bg-primary text-on-primary hover:bg-primary-fixed hover:text-primary transition-colors rounded-xl font-label-md text-label-md shadow-md">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Budget Modal -->
    <div x-show="showEditBudgetModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div @click.away="showEditBudgetModal = false; selectedBudget = { id: '', categorie: '', montant_limite: '' }" class="bg-surface-container-lowest text-on-surface w-full max-w-md rounded-2xl shadow-2xl p-lg flex flex-col gap-md relative border border-outline-variant/20" style="max-height: 85vh; overflow-y: auto;">
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-2">Modifier le Budget</h3>
            <template x-if="selectedBudget.id">
                <form :action="'/budgets/' + selectedBudget.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    @honeypot
                    
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Catégorie</label>
                        <input type="text" x-model="selectedBudget.categorie" class="w-full bg-surface-container/50 border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md text-on-surface-variant font-medium focus:outline-none" disabled>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md mb-xs text-on-surface-variant">Limite mensuelle (FCFA) <span class="text-red-500 font-bold">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="montant_limite" x-model="selectedBudget.montant_limite" class="w-full bg-surface-container border border-outline-variant/30 rounded-xl px-4 py-2 text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary" required>
                    </div>

                    <div class="flex gap-md pt-md">
                        <button type="button" @click="showEditBudgetModal = false; selectedBudget = { id: '', categorie: '', montant_limite: '' }" class="flex-grow py-3 bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors rounded-xl font-label-md text-label-md">
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

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Dynamic transition-in for progress bars
    document.querySelectorAll('[style*="width:"]').forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 1.5s cubic-bezier(0.22, 1, 0.36, 1)';
            bar.style.width = targetWidth;
        }, 100);
    });
});
</script>
@endpush
