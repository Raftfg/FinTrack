<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('personne');
            $table->decimal('montant_initial', 15, 2);
            $table->decimal('montant_rembourse', 15, 2)->default(0);
            $table->enum('statut', ['non_rembourse', 'partiel', 'rembourse'])
                ->default('non_rembourse');
            $table->date('date_pret');
            $table->date('date_echeance')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
