<?php

namespace Database\Factories;

use App\Modules\Portefeuille\Models\Portefeuille;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portefeuille>
 */
class PortefeuilleFactory extends Factory
{
    protected $model = Portefeuille::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'devise' => config('kaniapicks.devise_defaut'),
            'solde_disponible_cents' => fake()->numberBetween(0, 5_000_000_00),
            'solde_engage_cents' => 0,
            'solde_en_attente_retrait_cents' => 0,
            'solde_bonus_cents' => fake()->boolean(20) ? fake()->numberBetween(1_000_00, 20_000_00) : 0,
        ];
    }
}
