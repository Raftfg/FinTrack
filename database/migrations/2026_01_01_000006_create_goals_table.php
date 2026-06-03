<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('montant_cible', 15, 2);
            $table->decimal('montant_actuel', 15, 2)->default(0);
            $table->date('date_cible')->nullable();
            $table->boolean('atteint')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'atteint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
