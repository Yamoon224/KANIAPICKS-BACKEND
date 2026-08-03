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
        Schema::create('ecritures_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portefeuille_id')->constrained('portefeuilles')->cascadeOnDelete();
            $table->enum('type', ['depot', 'achat', 'vente', 'gain', 'frais', 'retrait', 'bonus', 'remboursement']);
            $table->bigInteger('montant_cents');
            $table->unsignedBigInteger('solde_apres_cents');
            $table->string('reference')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['portefeuille_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecritures_ledger');
    }
};
