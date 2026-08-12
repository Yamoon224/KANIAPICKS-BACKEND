<?php

namespace Database\Factories;

use App\Modules\Paiements\Models\Retrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Retrait>
 */
class RetraitFactory extends Factory
{
    protected $model = Retrait::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'operateur' => fake()->randomElement(['orange_money', 'mtn_momo', 'moov_money', 'wave', 'mpesa', 'airtel_money']),
            'numero_destinataire' => fake()->numerify('+225 07 ## ## ## ##'),
            'montant_cents' => fake()->numberBetween(50_00, 300_000_00),
            'statut' => fake()->randomElement(['initie', 'en_attente', 'confirme', 'confirme', 'confirme', 'echoue', 'expire', 'rembourse']),
        ];
    }
}
