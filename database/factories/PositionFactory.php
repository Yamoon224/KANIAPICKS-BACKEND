<?php

namespace Database\Factories;

use App\Modules\Trading\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $quantite = fake()->numberBetween(1, 1_000);

        return [
            'quantite' => $quantite,
            'prix_revient_total_cents' => $quantite * fake()->numberBetween(10_000, 90_000),
        ];
    }
}
