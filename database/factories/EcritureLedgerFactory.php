<?php

namespace Database\Factories;

use App\Modules\Portefeuille\Models\EcritureLedger;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EcritureLedger>
 */
class EcritureLedgerFactory extends Factory
{
    protected $model = EcritureLedger::class;

    private const TYPES_CREDITEURS = ['depot', 'gain', 'bonus', 'remboursement'];

    private const TYPES_DEBITEURS = ['achat', 'vente', 'frais', 'retrait'];

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $type = fake()->randomElement([...self::TYPES_CREDITEURS, ...self::TYPES_DEBITEURS]);
        $signe = in_array($type, self::TYPES_CREDITEURS, true) ? 1 : -1;

        return [
            'type' => $type,
            'montant_cents' => $signe * fake()->numberBetween(1_000, 500_000),
            'solde_apres_cents' => fake()->numberBetween(0, 10_000_000),
            'reference' => fake()->boolean(70) ? Str::upper(Str::random(10)) : null,
        ];
    }
}
