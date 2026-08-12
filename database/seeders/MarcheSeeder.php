<?php

namespace Database\Seeders;

use App\Modules\Marches\Models\Marche;
use Database\Factories\MarcheFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarcheSeeder extends Seeder
{
    use WithoutModelEvents;

    public const NOMBRE = 500;

    private const CATEGORIES = [
        'football_can',
        'autres_sports',
        'politique_elections',
        'economie_fcfa',
        'musique_divertissement',
        'actualite_africaine',
        'international',
        'meteo_divers',
    ];

    // Pondérée vers "publie" pour que le fil de marchés ne soit pas vide.
    private const STATUTS = ['publie', 'publie', 'publie', 'resolu', 'brouillon'];

    public function run(): void
    {
        $maintenant = now()->toDateTimeString();

        $marches = collect(range(1, self::NOMBRE))->map(function () use ($maintenant) {
            $statut = fake()->randomElement(self::STATUTS);
            $estResolu = $statut === 'resolu';
            $echeanceAt = $estResolu
                ? fake()->dateTimeBetween('-90 days', '-1 days')
                : fake()->dateTimeBetween('now', '+90 days');

            return (new MarcheFactory)->raw([
                'categorie' => fake()->randomElement(self::CATEGORIES),
                'statut' => $statut,
                'echeance_at' => $echeanceAt->format('Y-m-d H:i:s'),
                // Simule un peu d'activité de trading pour ne pas afficher
                // uniquement des prix à 50/50 (cf. AmmMoteurCotationService).
                'q_oui' => fake()->numberBetween(-3_000, 3_000),
                'q_non' => fake()->numberBetween(-3_000, 3_000),
                'issue_gagnante' => $estResolu ? fake()->randomElement(['oui', 'non']) : null,
                'preuve_url' => $estResolu ? fake()->url() : null,
                'resolu_at' => $estResolu
                    ? fake()->dateTimeBetween($echeanceAt, 'now')->format('Y-m-d H:i:s')
                    : null,
                'created_at' => $maintenant,
                'updated_at' => $maintenant,
            ]);
        })->all();

        Marche::insert($marches);
    }
}
