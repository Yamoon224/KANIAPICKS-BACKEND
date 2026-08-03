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
        Schema::table('marches', function (Blueprint $table) {
            // État du teneur de marché automatisé (LMSR) : q_oui/q_non sont
            // les quantités nettes de parts vendues par le marché pour
            // chaque issue ; liquidite_b est le paramètre de liquidité.
            $table->bigInteger('q_oui')->default(0)->after('valeur_nominale_cents');
            $table->bigInteger('q_non')->default(0)->after('q_oui');
            $table->unsignedBigInteger('liquidite_b')->default(1000)->after('q_non');

            $table->enum('issue_gagnante', ['oui', 'non'])->nullable()->after('liquidite_b');
            $table->string('preuve_url')->nullable()->after('issue_gagnante');
            $table->timestamp('resolu_at')->nullable()->after('preuve_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marches', function (Blueprint $table) {
            $table->dropColumn(['q_oui', 'q_non', 'liquidite_b', 'issue_gagnante', 'preuve_url', 'resolu_at']);
        });
    }
};
