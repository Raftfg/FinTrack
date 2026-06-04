<?php

namespace App\Http\Controllers;

use App\Domain\Budget\Models\Budget;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    /**
     * Display the list of budgets for a specific month and year.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $mois = $request->integer('mois', now()->month);
        $annee = $request->integer('annee', now()->year);

        // Fetch budgets
        $budgets = Budget::where('user_id', $userId)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->get();

        // Fetch real expenses grouped by category for this month
        $expenses = Transaction::where('user_id', $userId)
            ->where('type', TransactionType::DEPENSE)
            ->whereMonth('date_transaction', $mois)
            ->whereYear('date_transaction', $annee)
            ->selectRaw('categorie, SUM(montant) as total')
            ->groupBy('categorie')
            ->pluck('total', 'categorie')
            ->toArray();

        // Calculate rates and map metadata
        $totalBudgetise = 0;
        $totalDepense = 0;
        $depassementsCount = 0;

        $budgets = $budgets->map(function ($budget) use ($expenses, &$totalBudgetise, &$totalDepense, &$depassementsCount) {
            $spent = $expenses[$budget->categorie] ?? 0.00;
            $limit = $budget->montant_limite;

            $pct = $limit > 0 ? (int) (($spent / $limit) * 100) : 0;
            $remaining = max(0, $limit - $spent);
            $isOver = $spent > $limit;

            if ($isOver) {
                $depassementsCount++;
            }

            $totalBudgetise += $limit;
            $totalDepense += $spent;

            // Dynamically inject values
            $budget->real_spent = $spent;
            $budget->pct = $pct;
            $budget->remaining = $remaining;
            $budget->is_over = $isOver;

            return $budget;
        });

        $totalRestant = max(0, $totalBudgetise - $totalDepense);

        // Standard categories defined in config
        $allCategories = config('fintrack.transaction_categories', []);

        // Filter out categories that already have a budget this month
        $existingCategories = $budgets->pluck('categorie')->toArray();
        $availableCategories = array_filter($allCategories, function ($cat) use ($existingCategories) {
            return !in_array($cat, $existingCategories);
        });

        return view('budgets.index', compact(
            'budgets',
            'mois',
            'annee',
            'totalBudgetise',
            'totalDepense',
            'totalRestant',
            'depassementsCount',
            'availableCategories',
            'allCategories'
        ));
    }

    /**
     * Store a newly created budget.
     */
    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $categories = config('fintrack.transaction_categories', []);

        $validated = $request->validate([
            'categorie' => ['required', 'string', 'max:50', Rule::in($categories)],
            'montant_limite' => ['required', 'numeric', 'min:0.01'],
            'mois' => ['required', 'integer', 'between:1,12'],
            'annee' => ['required', 'integer', 'between:2020,2100'],
        ]);

        // Check unique constraint
        $exists = Budget::where('user_id', $userId)
            ->where('categorie', $validated['categorie'])
            ->where('mois', $validated['mois'])
            ->where('annee', $validated['annee'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Un budget existe déjà pour cette catégorie ce mois-ci.');
        }

        $validated['user_id'] = $userId;
        Budget::create($validated);

        return back()->with('success', 'Budget défini avec succès !');
    }

    /**
     * Update the specified budget.
     */
    public function update(Request $request, int $id)
    {
        $budget = Budget::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'montant_limite' => ['required', 'numeric', 'min:0.01'],
        ]);

        $budget->update($validated);

        return back()->with('success', 'Budget mis à jour avec succès !');
    }

    /**
     * Remove the specified budget.
     */
    public function destroy(Request $request, int $id)
    {
        $budget = Budget::where('user_id', $request->user()->id)->findOrFail($id);
        $budget->delete();

        return back()->with('success', 'Budget supprimé.');
    }
}
