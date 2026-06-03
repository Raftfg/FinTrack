@extends('layouts.app')

@section('title', 'Mes Objectifs')

@section('content')

{{-- Flash message --}}
@if(session('success'))
<div class="bg-secondary-container text-on-secondary-container px-md py-sm rounded-xl text-label-md mb-md flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    {{ session('success') }}
</div>
@endif

<!-- Header Section -->
<div class="flex justify-between items-end mb-lg">
    <div>
        <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-xs font-semibold">Mes Objectifs</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Visualisez et gérez vos ambitions financières.</p>
    </div>
    <!-- Desktop Add Button -->
    <button @click="showGoalModal = true" class="hidden md:flex items-center gap-2 py-2 px-4 bg-primary text-on-primary rounded-xl hover:bg-primary-fixed hover:text-primary transition-all text-label-md font-semibold active:scale-95 shadow-md">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Nouvel objectif
    </button>
</div>

<!-- Bento Grid Layout -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-lg">

    <!-- Global Stats Card -->
    <div class="col-span-1 md:col-span-8 bg-surface-container-low p-lg rounded-xl shadow-sm border border-outline-variant/30 flex flex-col justify-between">
        <div>
            <span class="font-label-md text-label-md text-secondary uppercase tracking-wider mb-base block">Progression Totale</span>
            <div class="flex items-baseline gap-base">
                <span class="font-display-lg text-display-lg-mobile md:text-display-lg text-primary font-bold">{{ $progressionGlobale }}%</span>
                <span class="font-label-md text-label-md text-on-surface-variant">de l'objectif global</span>
            </div>
        </div>
        <div class="mt-lg">
            <div class="w-full bg-outline-variant h-4 rounded-full overflow-hidden">
                <div class="bg-primary h-full transition-all duration-1000 ease-out" style="width: {{ $progressionGlobale }}%"></div>
            </div>
            <div class="flex justify-between mt-sm">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Épargné : {{ number_format($totalActuel, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Cible : {{ number_format($totalCible, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
            </div>
        </div>
    </div>

    <!-- CTA Add Goal -->
    <div @click="showGoalModal = true" class="col-span-1 md:col-span-4 bg-primary text-on-primary p-lg rounded-xl shadow-lg flex flex-col items-center justify-center text-center group cursor-pointer active:scale-95 transition-transform">
        <div class="bg-on-primary/10 p-md rounded-full mb-md group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-4xl">add_circle</span>
        </div>
        <h3 class="font-headline-sm text-headline-sm mb-xs font-semibold">Nouvel Objectif</h3>
        <p class="font-body-sm text-body-sm opacity-80">Commencez un nouveau projet d'épargne aujourd'hui.</p>
    </div>

    <!-- Goal Cards from DB -->
    @forelse($goals as $goal)
    @php
        $pct = $goal->montant_cible > 0
            ? min(100, (int)(($goal->montant_actuel / $goal->montant_cible) * 100))
            : 0;
        $icons = ['savings' => 'emergency', 'home' => 'home', 'school' => 'school', 'voyage' => 'flight', 'voiture' => 'directions_car', 'urgence' => 'emergency', 'retraite' => 'elderly', 'mariage' => 'favorite'];
        $icon  = 'flag';
        foreach($icons as $keyword => $ic) {
            if(stripos($goal->nom, $keyword) !== false || stripos($goal->description ?? '', $keyword) !== false) {
                $icon = $ic; break;
            }
        }
        $barColor = $goal->atteint ? 'bg-secondary' : ($pct >= 80 ? 'bg-secondary' : ($pct >= 40 ? 'bg-primary' : 'bg-tertiary'));
        $goalData = json_encode([
            'id'             => $goal->id,
            'nom'            => $goal->nom,
            'description'    => $goal->description,
            'montant_cible'  => $goal->montant_cible,
            'montant_actuel' => $goal->montant_actuel,
            'date_cible'     => $goal->date_cible?->toDateString(),
            'atteint'        => $goal->atteint,
        ]);
    @endphp
    <div class="col-span-1 md:col-span-4 bg-surface-container-lowest p-lg rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-md transition-shadow flex flex-col {{ $goal->atteint ? 'opacity-80' : '' }}">
        <div class="flex justify-between items-start mb-md">
            <div class="bg-surface-container p-sm rounded-lg">
                <span class="material-symbols-outlined text-primary">{{ $icon }}</span>
            </div>
            <div class="flex items-center gap-sm">
                <span class="font-label-md text-label-md px-base py-xs font-bold
                    {{ $goal->atteint ? 'bg-secondary-fixed text-on-secondary-fixed' : 'bg-primary-fixed text-on-primary-fixed' }} rounded-full">
                    {{ $pct }}%
                </span>
                @if($goal->atteint)
                <span class="material-symbols-outlined text-secondary text-[18px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                @endif
            </div>
        </div>

        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-base font-semibold">{{ $goal->nom }}</h3>
        @if($goal->description)
        <p class="font-body-sm text-body-sm text-on-surface-variant mb-sm">{{ $goal->description }}</p>
        @endif

        <div class="space-y-sm mb-lg flex-1">
            <div class="flex justify-between">
                <span class="font-body-sm text-body-sm text-on-surface-variant">Actuel</span>
                <span class="font-label-md text-label-md text-on-surface font-semibold">{{ number_format($goal->montant_actuel, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-body-sm text-body-sm text-on-surface-variant">Cible</span>
                <span class="font-label-md text-label-md text-on-surface font-semibold">{{ number_format($goal->montant_cible, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
            </div>
            @if($goal->date_cible)
            <div class="flex justify-between">
                <span class="font-body-sm text-body-sm text-on-surface-variant">Échéance</span>
                <span class="font-label-md text-label-md text-on-surface font-semibold">{{ $goal->date_cible->locale('fr')->isoFormat('D MMM YYYY') }}</span>
            </div>
            @endif
        </div>

        <div class="w-full bg-surface-container-highest h-2 rounded-full mb-md overflow-hidden">
            <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $pct }}%"></div>
        </div>

        <div class="flex gap-sm">
            <button
                @click="selectedGoal = {{ $goalData }}; showGoalDetailModal = true; showGoalAddSavings = false"
                class="flex-1 py-2 font-label-md text-label-md text-primary border border-primary rounded-lg hover:bg-primary hover:text-on-primary transition-colors font-semibold"
            >
                @if($goal->atteint) Détails @else Épargner @endif
            </button>
            <button
                @click="selectedGoal = {{ $goalData }}; showGoalEditModal = true"
                class="p-2 border border-outline-variant rounded-lg hover:bg-surface-container transition-colors"
                title="Modifier l'objectif"
            >
                <span class="material-symbols-outlined text-[18px]">edit</span>
            </button>
            <form method="POST" action="{{ route('goals.destroy', $goal->id) }}">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Supprimer cet objectif ?')" class="p-2 border border-error-container text-error rounded-lg hover:bg-error-container transition-colors">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-1 md:col-span-12 flex flex-col items-center justify-center py-16 text-center">
        <span class="material-symbols-outlined text-6xl text-outline-variant mb-md">flag</span>
        <p class="font-headline-sm text-headline-sm text-on-surface-variant font-bold">Aucun objectif défini</p>
        <p class="font-body-sm text-body-sm text-outline mt-xs">Commencez par créer votre premier objectif financier.</p>
        <button @click="showGoalModal = true" class="mt-md flex items-center gap-2 py-2 px-4 bg-primary text-on-primary rounded-xl text-label-md font-semibold active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Créer un objectif
        </button>
    </div>
    @endforelse

    <!-- Analyse Prédictive Banner (si données présentes) -->
    @if($goals->isNotEmpty() && $progressionGlobale > 0)
    <div class="col-span-1 md:col-span-12 bg-inverse-surface text-inverse-on-surface p-lg rounded-xl flex flex-col md:flex-row items-center gap-lg">
        <div class="flex-1">
            <h3 class="font-headline-md text-headline-md mb-xs font-semibold">Analyse Prédictive</h3>
            <p class="font-body-md text-body-md opacity-80">
                Vous avez atteint <strong>{{ $progressionGlobale }}%</strong> de votre objectif global.
                Continuez sur cette lancée pour atteindre vos cibles financières.
            </p>
        </div>
        <div class="flex gap-md">
            <div class="text-center p-md bg-white/10 rounded-lg backdrop-blur-sm">
                <span class="block font-headline-sm text-headline-sm font-bold">{{ $goals->where('atteint', true)->count() }}/{{ $goals->count() }}</span>
                <span class="text-label-sm opacity-60">Atteints</span>
            </div>
            <div class="text-center p-md bg-white/10 rounded-lg backdrop-blur-sm">
                <span class="block font-headline-sm text-headline-sm font-bold">{{ number_format($totalCible - $totalActuel, 0, ',', ' ') }} {{ config('fintrack.currency_symbol', 'FCFA') }}</span>
                <span class="text-label-sm opacity-60">Restants</span>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
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
