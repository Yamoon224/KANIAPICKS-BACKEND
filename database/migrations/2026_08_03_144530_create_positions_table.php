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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marche_id')->constrained('marches')->cascadeOnDelete();
            $table->enum('issue', ['oui', 'non']);
            $table->unsignedBigInteger('quantite')->default(0);
            $table->unsignedBigInteger('prix_revient_total_cents')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'marche_id', 'issue']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
