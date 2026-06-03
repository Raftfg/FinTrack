<?php

namespace App\Http\Controllers;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $mois   = $request->integer('mois', now()->month);
        $annee  = $request->integer('annee', now()->year);
        $type   = $request->get('type');

        $query = Transaction::where('user_id', $userId)
            ->whereMonth('date_transaction', $mois)
            ->whereYear('date_transaction', $annee)
            ->orderByDesc('date_transaction');

        if ($type && TransactionType::tryFrom($type)) {
            $query->where('type', $type);
        }

        $transactions = $query->get();

        // Grouper par date pour affichage
        $grouped = $transactions->groupBy(fn ($t) => $t->date_transaction->format('Y-m-d'));

        // Totaux du mois
        $totalRevenus  = $transactions->filter(fn($t) => $t->type === TransactionType::REVENU)->sum('montant');
        $totalDepenses = $transactions->filter(fn($t) => $t->type === TransactionType::DEPENSE)->sum('montant');

        return view('transactions.index', compact(
            'grouped', 'mois', 'annee', 'type',
            'totalRevenus', 'totalDepenses'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:revenu,depense,epargne,pret,remboursement',
            'montant' => 'required|numeric|min:0.01',
            'categorie' => 'required|string|max:255',
            'date_transaction' => 'required|date',
            'note' => 'nullable|string|max:255',
            'loan_id' => 'nullable|exists:loans,id',
        ]);

        $validated['user_id'] = $request->user()->id;

        Transaction::create($validated);

        return back()->with('success', 'Transaction ajoutée avec succès !');
    }

    public function update(Request $request, int $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string|in:revenu,depense,epargne,pret,remboursement',
            'montant' => 'required|numeric|min:0.01',
            'categorie' => 'required|string|max:255',
            'date_transaction' => 'required|date',
            'note' => 'nullable|string|max:255',
            'loan_id' => 'nullable|exists:loans,id',
        ]);

        $transaction->update($validated);

        return back()->with('success', 'Transaction mise à jour avec succès !');
    }

    public function destroy(Request $request, int $id)
    {
        Transaction::where('user_id', $request->user()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Transaction supprimée.');
    }

    public function exportExcel(Request $request)
    {
        $userId = $request->user()->id;
        $mois   = $request->integer('mois', now()->month);
        $annee  = $request->integer('annee', now()->year);
        $type   = $request->get('type');

        $query = Transaction::where('user_id', $userId)
            ->whereMonth('date_transaction', $mois)
            ->whereYear('date_transaction', $annee)
            ->orderByDesc('date_transaction');

        if ($type && TransactionType::tryFrom($type)) {
            $query->where('type', $type);
        }

        $transactions = $query->get();

        $fileName = sprintf('transactions_%s_%s.csv', $annee, str_pad($mois, 2, '0', STR_PAD_LEFT));

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Date', 'Type', 'Montant (FCFA)', 'Categorie', 'Note'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';');

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->date_transaction->format('Y-m-d'),
                    $t->type->label(),
                    $t->montant,
                    $t->categorie,
                    $t->note ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $userId = $request->user()->id;
        $mois   = $request->integer('mois', now()->month);
        $annee  = $request->integer('annee', now()->year);
        $type   = $request->get('type');

        $query = Transaction::where('user_id', $userId)
            ->whereMonth('date_transaction', $mois)
            ->whereYear('date_transaction', $annee)
            ->orderByDesc('date_transaction');

        if ($type && TransactionType::tryFrom($type)) {
            $query->where('type', $type);
        }

        $transactions = $query->get();

        $monthName = \Carbon\Carbon::create(null, $mois)->locale('fr')->isoFormat('MMMM');

        $totalRevenus  = $transactions->filter(fn($t) => $t->type === TransactionType::REVENU)->sum('montant');
        $totalDepenses = $transactions->filter(fn($t) => $t->type === TransactionType::DEPENSE)->sum('montant');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transactions.pdf', [
            'transactions' => $transactions,
            'mois' => $monthName,
            'annee' => $annee,
            'totalRevenus' => $totalRevenus,
            'totalDepenses' => $totalDepenses,
            'typeLabel' => $type ? TransactionType::tryFrom($type)?->label() : null,
            'user' => $request->user()
        ]);

        $fileName = sprintf('transactions_%s_%s.pdf', $annee, str_pad($mois, 2, '0', STR_PAD_LEFT));
        return $pdf->download($fileName);
    }
}

