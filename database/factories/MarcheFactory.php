<?php

namespace Database\Factories;

use App\Modules\Marches\Models\Marche;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Marche>
 */
class MarcheFactory extends Factory
{
    protected $model = Marche::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence().' ?',
            'categorie' => 'actualite_africaine',
            'statut' => 'publie',
            'regle_resolution' => fake()->paragraph(),
            'source_officielle' => fake()->url(),
            'echeance_at' => now()->addDays(30),
            'valeur_nominale_cents' => 100_000,
            'q_oui' => 0,
            'q_non' => 0,
            'liquidite_b' => 1000,
        ];
    }
}
