<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('categorie', 50);
            $table->decimal('montant_limite', 15, 2);
            $table->unsignedTinyInteger('mois');   // 1-12
            $table->unsignedSmallInteger('annee');
            $table->timestamps();

            $table->unique(['user_id', 'categorie', 'mois', 'annee']);
            $table->index(['user_id', 'mois', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
