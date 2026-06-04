<?php

use App\Domain\Budget\Models\Budget;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('budget screen can be rendered for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/budgets');

    $response->assertStatus(200);
});

test('a user can define a budget', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/budgets', [
        'categorie' => 'Alimentation',
        'montant_limite' => 500,
        'mois' => 6,
        'annee' => 2026,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('budgets', [
        'user_id' => $user->id,
        'categorie' => 'Alimentation',
        'montant_limite' => 500,
        'mois' => 6,
        'annee' => 2026,
    ]);
});

test('a user cannot define duplicate budgets for the same month/category', function () {
    $user = User::factory()->create();
    Budget::create([
        'user_id' => $user->id,
        'categorie' => 'Alimentation',
        'montant_limite' => 500,
        'mois' => 6,
        'annee' => 2026,
    ]);

    $response = $this->actingAs($user)->from('/budgets')->post('/budgets', [
        'categorie' => 'Alimentation',
        'montant_limite' => 800,
        'mois' => 6,
        'annee' => 2026,
    ]);

    $response->assertRedirect();
    $this->assertEquals(1, Budget::count());
});

test('a user can update a budget limit', function () {
    $user = User::factory()->create();
    $budget = Budget::create([
        'user_id' => $user->id,
        'categorie' => 'Alimentation',
        'montant_limite' => 500,
        'mois' => 6,
        'annee' => 2026,
    ]);

    $response = $this->actingAs($user)->patch("/budgets/{$budget->id}", [
        'montant_limite' => 700,
    ]);

    $response->assertRedirect();
    $this->assertEquals(700, $budget->fresh()->montant_limite);
});

test('a user can delete a budget', function () {
    $user = User::factory()->create();
    $budget = Budget::create([
        'user_id' => $user->id,
        'categorie' => 'Alimentation',
        'montant_limite' => 500,
        'mois' => 6,
        'annee' => 2026,
    ]);

    $response = $this->actingAs($user)->delete("/budgets/{$budget->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
});

test('creating transaction triggers budget warnings and alert notifications', function () {
    $user = User::factory()->create();
    
    // Create a budget
    Budget::create([
        'user_id' => $user->id,
        'categorie' => 'Alimentation',
        'montant_limite' => 1000,
        'mois' => 6,
        'annee' => 2026,
    ]);

    // 1. Transaction below 80% (e.g. 500) - No notification
    Transaction::create([
        'user_id' => $user->id,
        'type' => TransactionType::DEPENSE,
        'montant' => 500,
        'categorie' => 'Alimentation',
        'date_transaction' => '2026-06-04',
    ]);

    $this->assertEquals(0, $user->unreadNotifications()->count());

    // 2. Transaction reaching 80% (e.g. additional 350, total 850) - triggers warning
    Transaction::create([
        'user_id' => $user->id,
        'type' => TransactionType::DEPENSE,
        'montant' => 350,
        'categorie' => 'Alimentation',
        'date_transaction' => '2026-06-05',
    ]);

    $this->assertEquals(1, $user->unreadNotifications()->count());
    $this->assertStringContainsString('80%', $user->unreadNotifications()->first()->data['title']);

    // Clear notifications
    $user->unreadNotifications()->delete();

    // 3. Transaction reaching 100% (e.g. additional 200, total 1050) - triggers error
    Transaction::create([
        'user_id' => $user->id,
        'type' => TransactionType::DEPENSE,
        'montant' => 200,
        'categorie' => 'Alimentation',
        'date_transaction' => '2026-06-06',
    ]);

    $this->assertEquals(1, $user->unreadNotifications()->count());
    $this->assertStringContainsString('Dépassé', $user->unreadNotifications()->first()->data['title']);
});
