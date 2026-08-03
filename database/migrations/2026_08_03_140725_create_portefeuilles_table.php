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
        Schema::create('portefeuilles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('devise', 3)->default('XOF');
            $table->unsignedBigInteger('solde_disponible_cents')->default(0);
            $table->unsignedBigInteger('solde_engage_cents')->default(0);
            $table->unsignedBigInteger('solde_en_attente_retrait_cents')->default(0);
            $table->unsignedBigInteger('solde_bonus_cents')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portefeuilles');
    }
};
