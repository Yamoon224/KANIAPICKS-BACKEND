<?php

namespace Database\Factories;

use App\Modules\Trading\Models\Ordre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ordre>
 */
class OrdreFactory extends Factory
{
    protected $model = Ordre::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'sens' => fake()->randomElement(['achat', 'vente']),
            'issue' => fake()->randomElement(['oui', 'non']),
            'quantite' => fake()->numberBetween(1, 500),
            'prix_cents' => fake()->numberBetween(1_000, 99_000),
            'frais_cents' => fake()->numberBetween(0, 5_000),
        ];
    }
}
