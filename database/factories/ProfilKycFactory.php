<?php

namespace Database\Factories;

use App\Modules\Conformite\Models\ProfilKyc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfilKyc>
 */
class ProfilKycFactory extends Factory
{
    protected $model = ProfilKyc::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $palier = fake()->numberBetween(1, 3);

        return [
            'palier' => $palier,
            'statut' => fake()->randomElement(['non_soumis', 'en_revue', 'approuve', 'rejete']),
            'plafond_depot_journalier_cents' => config("kaniapicks.plafonds_kyc.{$palier}.depot"),
            'plafond_retrait_journalier_cents' => config("kaniapicks.plafonds_kyc.{$palier}.retrait"),
        ];
    }
}
