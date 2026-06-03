<?php

namespace Database\Seeders;

use App\Domain\Budget\Models\Budget;
use App\Domain\Goal\Models\Goal;
use App\Domain\Loan\Enums\LoanStatus;
use App\Domain\Loan\Models\Loan;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Création de l'utilisateur de test
        $user = User::updateOrCreate(
            ['email' => 'admin@fintrack.com'],
            [
                'name' => 'Rafiou',
                'password' => Hash::make('password'),
                'currency' => 'EUR',
                'currency_symbol' => '€',
                'is_admin' => true,
                'status' => 'active',
                'tier' => 'enterprise',
            ]
        );

        // Création des autres utilisateurs de test (Stitch)
        User::updateOrCreate(
            ['email' => 'elena.r@financely.com'],
            [
                'name' => 'Elena Rodriguez',
                'password' => Hash::make('password'),
                'currency' => 'EUR',
                'currency_symbol' => '€',
                'is_admin' => false,
                'status' => 'active',
                'tier' => 'premium',
            ]
        );

        User::updateOrCreate(
            ['email' => 'm.sterling@agency.io'],
            [
                'name' => 'Marcus Sterling',
                'password' => Hash::make('password'),
                'currency' => 'USD',
                'currency_symbol' => '$',
                'is_admin' => false,
                'status' => 'suspended',
                'tier' => 'free',
            ]
        );

        User::updateOrCreate(
            ['email' => 'sarah.chen@tech.com'],
            [
                'name' => 'Sarah Chen',
                'password' => Hash::make('password'),
                'currency' => 'USD',
                'currency_symbol' => '$',
                'is_admin' => false,
                'status' => 'active',
                'tier' => 'enterprise',
            ]
        );

        User::updateOrCreate(
            ['email' => 'j.smith@freelance.org'],
            [
                'name' => 'Jordan Smith',
                'password' => Hash::make('password'),
                'currency' => 'EUR',
                'currency_symbol' => '€',
                'is_admin' => false,
                'status' => 'active',
                'tier' => 'premium',
            ]
        );

        $userId = $user->id;

        // Vider les données existantes pour éviter les doublons lors des re-seeds
        Transaction::where('user_id', $userId)->delete();
        Loan::where('user_id', $userId)->delete();
        Budget::where('user_id', $userId)->delete();
        Goal::where('user_id', $userId)->delete();

        // 2. Création des Prêts
        $loan1 = Loan::create([
            'user_id' => $userId,
            'personne' => 'Jean Dupont',
            'montant_initial' => 5000,
            'montant_rembourse' => 3800,
            'statut' => LoanStatus::PARTIEL,
            'date_pret' => Carbon::now()->subMonths(6),
            'note' => 'Prêt personnel pour école',
        ]);

        $loan2 = Loan::create([
            'user_id' => $userId,
            'personne' => 'Alice Lemaire',
            'montant_initial' => 850,
            'montant_rembourse' => 0,
            'statut' => LoanStatus::NON_REMBOURSE,
            'date_pret' => Carbon::now()->subMonths(2),
            'note' => 'Aide urgence loyer',
        ]);

        Loan::create([
            'user_id' => $userId,
            'personne' => 'Marc Bernard',
            'montant_initial' => 1200,
            'montant_rembourse' => 1200,
            'statut' => LoanStatus::REMBOURSE,
            'date_pret' => Carbon::now()->subYear(),
            'note' => 'Prêt voyage',
        ]);

        // 3. Création des Transactions
        $transactions = [
            // Revenus
            ['type' => TransactionType::REVENU, 'montant' => 3200.00, 'categorie' => 'Salaire', 'date_transaction' => Carbon::now()->startOfMonth()->addDays(2), 'note' => 'Virement Salaire'],
            ['type' => TransactionType::REVENU, 'montant' => 150.00, 'categorie' => 'Vente', 'date_transaction' => Carbon::now()->subDays(15), 'note' => 'Vente Leboncoin'],
            
            // Dépenses
            ['type' => TransactionType::DEPENSE, 'montant' => 850.00, 'categorie' => 'Logement', 'date_transaction' => Carbon::now()->startOfMonth()->addDays(4), 'note' => 'Loyer'],
            ['type' => TransactionType::DEPENSE, 'montant' => 120.50, 'categorie' => 'Alimentation', 'date_transaction' => Carbon::now()->subDays(2), 'note' => 'Courses Supermarché'],
            ['type' => TransactionType::DEPENSE, 'montant' => 45.00, 'categorie' => 'Loisirs', 'date_transaction' => Carbon::now()->subDays(5), 'note' => 'Restaurant'],
            ['type' => TransactionType::DEPENSE, 'montant' => 89.99, 'categorie' => 'Électronique', 'date_transaction' => Carbon::now(), 'note' => 'Achat Amazon'],
            ['type' => TransactionType::DEPENSE, 'montant' => 60.00, 'categorie' => 'Transport', 'date_transaction' => Carbon::now()->subDays(10), 'note' => 'Plein essence'],
            
            // Épargne
            ['type' => TransactionType::EPARGNE, 'montant' => 500.00, 'categorie' => 'Épargne', 'date_transaction' => Carbon::now()->startOfMonth()->addDays(5), 'note' => 'Virement Livret A'],
            
            // Remboursements de prêts (liés aux prêts existants)
            ['type' => TransactionType::REMBOURSEMENT, 'montant' => 200.00, 'categorie' => 'Remboursement Prêt', 'date_transaction' => Carbon::now()->subDays(20), 'loan_id' => $loan1->id, 'note' => 'Remboursement mensuel Jean'],
        ];

        foreach ($transactions as $t) {
            Transaction::create(array_merge($t, ['user_id' => $userId]));
        }

        // 4. Création des Budgets
        $budgets = [
            ['categorie' => 'Alimentation', 'montant_limite' => 400.00, 'mois' => Carbon::now()->month, 'annee' => Carbon::now()->year],
            ['categorie' => 'Logement', 'montant_limite' => 900.00, 'mois' => Carbon::now()->month, 'annee' => Carbon::now()->year],
            ['categorie' => 'Loisirs', 'montant_limite' => 150.00, 'mois' => Carbon::now()->month, 'annee' => Carbon::now()->year],
        ];

        foreach ($budgets as $b) {
            Budget::create(array_merge($b, ['user_id' => $userId]));
        }

        // 5. Création des Objectifs
        Goal::create([
            'user_id' => $userId,
            'nom' => 'Fonds d\'urgence',
            'description' => 'Matelas de sécurité de 3 mois',
            'montant_cible' => 15000.00,
            'montant_actuel' => 12750.00,
            'date_cible' => Carbon::now()->endOfYear(),
            'atteint' => false,
        ]);

        Goal::create([
            'user_id' => $userId,
            'nom' => 'Achat maison',
            'description' => 'Apport pour crédit immobilier',
            'montant_cible' => 50000.00,
            'montant_actuel' => 21000.00,
            'date_cible' => Carbon::now()->addYears(2),
            'atteint' => false,
        ]);

        Goal::create([
            'user_id' => $userId,
            'nom' => 'Voyage Japon',
            'description' => 'Vacances été prochain',
            'montant_cible' => 4500.00,
            'montant_actuel' => 4500.00,
            'date_cible' => Carbon::now()->subMonth(),
            'atteint' => true,
        ]);
    }
}
