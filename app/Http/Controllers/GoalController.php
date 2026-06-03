<?php

namespace App\Http\Controllers;

use App\Domain\Goal\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $goals = Goal::where('user_id', $userId)
            ->orderBy('atteint')
            ->orderBy('date_cible')
            ->get();

        $totalCible   = $goals->sum('montant_cible');
        $totalActuel  = $goals->sum('montant_actuel');
        $progressionGlobale = $totalCible > 0
            ? number_format(($totalActuel / $totalCible) * 100, 1)
            : 0;

        return view('goals.index', compact('goals', 'totalCible', 'totalActuel', 'progressionGlobale'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'montant_cible' => 'required|numeric|min:0.01',
            'montant_actuel' => 'nullable|numeric|min:0',
            'date_cible' => 'nullable|date',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['montant_actuel'] = $validated['montant_actuel'] ?? 0.00;
        $validated['atteint'] = $validated['montant_actuel'] >= $validated['montant_cible'];

        Goal::create($validated);

        return back()->with('success', 'Objectif créé avec succès !');
    }

    public function update(Request $request, int $id)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'montant_actuel'  => 'nullable|numeric|min:0',
            'nom'             => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:255',
            'montant_cible'   => 'nullable|numeric|min:0.01',
            'date_cible'      => 'nullable|date',
            'ajout_epargne'   => 'nullable|numeric|min:0',
        ]);

        // If user is adding savings incrementally
        if (isset($validated['ajout_epargne']) && $validated['ajout_epargne'] > 0) {
            $goal->montant_actuel = $goal->montant_actuel + $validated['ajout_epargne'];
        } elseif (isset($validated['montant_actuel'])) {
            $goal->montant_actuel = $validated['montant_actuel'];
        }

        if (isset($validated['nom']))           $goal->nom           = $validated['nom'];
        if (isset($validated['description']))   $goal->description   = $validated['description'];
        if (isset($validated['montant_cible'])) $goal->montant_cible = $validated['montant_cible'];
        if (array_key_exists('date_cible', $validated)) $goal->date_cible = $validated['date_cible'];

        $goal->atteint = $goal->montant_actuel >= $goal->montant_cible;
        $goal->save();

        return back()->with('success', 'Objectif mis à jour avec succès !');
    }

    public function destroy(Request $request, int $id)
    {
        Goal::where('user_id', $request->user()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Objectif supprimé.');
    }
}

