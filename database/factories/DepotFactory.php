<?php

namespace Database\Factories;

use App\Modules\Paiements\Models\Depot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Depot>
 */
class DepotFactory extends Factory
{
    protected $model = Depot::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'operateur' => fake()->randomElement(['orange_money', 'mtn_momo', 'moov_money', 'wave', 'mpesa', 'airtel_money']),
            'montant_cents' => fake()->numberBetween(50_00, 500_000_00),
            'statut' => fake()->randomElement(['initie', 'en_attente', 'confirme', 'confirme', 'confirme', 'echoue', 'expire', 'rembourse']),
            // Unique, comme exigé par la contrainte de la table ; l'UUID
            // évite toute collision, y compris entre plusieurs exécutions.
            'reference_agregateur' => (string) Str::uuid(),
        ];
    }
}
