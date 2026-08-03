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
        Schema::create('profils_kyc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('palier')->default(1);
            $table->enum('statut', ['non_soumis', 'en_revue', 'approuve', 'rejete'])->default('non_soumis');
            $table->unsignedBigInteger('plafond_depot_journalier_cents');
            $table->unsignedBigInteger('plafond_retrait_journalier_cents');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profils_kyc');
    }
};
