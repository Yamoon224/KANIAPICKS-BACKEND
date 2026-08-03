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
        Schema::create('marches', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('categorie');
            $table->string('statut')->default('brouillon');
            $table->text('regle_resolution');
            $table->string('source_officielle');
            $table->timestamp('echeance_at');
            $table->unsignedBigInteger('valeur_nominale_cents')->default(100000);
            $table->timestamps();

            $table->index(['statut', 'categorie']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marches');
    }
};
