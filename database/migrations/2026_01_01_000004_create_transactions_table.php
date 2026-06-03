<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'revenu', 'depense', 'epargne', 'pret', 'remboursement',
            ]);
            $table->decimal('montant', 15, 2);
            $table->string('categorie', 50);
            $table->date('date_transaction');
            $table->text('note')->nullable();
            $table->foreignId('loan_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'date_transaction']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
