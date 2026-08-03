<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ordres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marche_id')->constrained('marches')->cascadeOnDelete();
            $table->enum('sens', ['achat', 'vente']);
            $table->enum('issue', ['oui', 'non']);
            $table->unsignedBigInteger('quantite');
            $table->unsignedBigInteger('prix_cents');
            $table->unsignedBigInteger('frais_cents')->default(0);
            $table->timestamps();

            $table->index(['marche_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordres');
    }
};
