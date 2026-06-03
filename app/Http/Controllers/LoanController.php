<?php

namespace App\Http\Controllers;

use App\Domain\Loan\Enums\LoanStatus;
use App\Domain\Loan\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $loans = Loan::where('user_id', $userId)
            ->orderByRaw("CASE statut
                WHEN 'non_rembourse' THEN 0
                WHEN 'partiel' THEN 1
                WHEN 'rembourse' THEN 2
                ELSE 3 END")
            ->orderByDesc('date_pret')
            ->get();

        $totalDu      = $loans->sum('montant_initial');
        $totalRestant = $loans->sum(fn($l) => $l->montant_initial - $l->montant_rembourse);
        $tauxGlobal   = $totalDu > 0
            ? (int) (($loans->sum('montant_rembourse') / $totalDu) * 100)
            : 0;

        return view('loans.index', compact('loans', 'totalDu', 'totalRestant', 'tauxGlobal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'personne' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:30',
            'montant_initial' => 'required|numeric|min:0.01',
            'montant_rembourse' => 'nullable|numeric|min:0',
            'statut' => 'required|string|in:non_rembourse,partiel,rembourse',
            'date_pret' => 'required|date',
            'date_echeance' => 'nullable|date|after_or_equal:date_pret',
            'note' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['montant_rembourse'] = $validated['montant_rembourse'] ?? 0.00;

        Loan::create($validated);

        return back()->with('success', 'Prêt enregistré avec succès !');
    }

    public function update(Request $request, int $id)
    {
        $loan = Loan::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'personne'          => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'telephone'         => 'nullable|string|max:30',
            'montant_initial'   => 'nullable|numeric|min:0.01',
            'montant_rembourse' => 'nullable|numeric|min:0',
            'ajout_remboursement' => 'nullable|numeric|min:0',
            'statut'            => 'nullable|string|in:non_rembourse,partiel,rembourse',
            'date_echeance'     => 'nullable|date',
            'note'              => 'nullable|string|max:255',
        ]);

        // Add to reimbursed amount incrementally
        if (isset($validated['ajout_remboursement']) && $validated['ajout_remboursement'] > 0) {
            $loan->montant_rembourse = min(
                $loan->montant_initial,
                $loan->montant_rembourse + $validated['ajout_remboursement']
            );
        } elseif (isset($validated['montant_rembourse'])) {
            $loan->montant_rembourse = min($loan->montant_initial, $validated['montant_rembourse']);
        }

        if (isset($validated['personne']))        $loan->personne        = $validated['personne'];
        if (array_key_exists('email', $validated)) $loan->email          = $validated['email'];
        if (array_key_exists('telephone', $validated)) $loan->telephone  = $validated['telephone'];
        if (isset($validated['montant_initial'])) $loan->montant_initial = $validated['montant_initial'];
        if (isset($validated['statut']))          $loan->statut          = $validated['statut'];
        if (array_key_exists('date_echeance', $validated)) $loan->date_echeance = $validated['date_echeance'];
        if (array_key_exists('note', $validated)) $loan->note = $validated['note'];

        // Auto-update status based on repayment
        if ($loan->montant_rembourse >= $loan->montant_initial) {
            $loan->statut = LoanStatus::REMBOURSE;
        } elseif ($loan->montant_rembourse > 0 && !isset($validated['statut'])) {
            $loan->statut = LoanStatus::PARTIEL;
        }

        $loan->save();

        return back()->with('success', 'Prêt mis à jour avec succès !');
    }

    public function destroy(Request $request, int $id)
    {
        Loan::where('user_id', $request->user()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Prêt supprimé.');
    }
}

