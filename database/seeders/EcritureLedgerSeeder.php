<?php

namespace Database\Seeders;

use App\Modules\Portefeuille\Models\EcritureLedger;
use App\Modules\Portefeuille\Models\Portefeuille;
use Database\Factories\EcritureLedgerFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class EcritureLedgerSeeder extends Seeder
{
    use WithoutModelEvents;

    public const NOMBRE = 500;

    public function run(): void
    {
        $idsPortefeuilles = Portefeuille::query()->pluck('id')->all();

        if ($idsPortefeuilles === []) {
            $this->command?->warn('EcritureLedgerSeeder ignoré : aucun portefeuille en base.');

            return;
        }

        // La table n'a pas de colonne `updated_at` (écritures immuables,
        // append-only) : seul `created_at` est renseigné.
        $ecritures = collect(range(1, self::NOMBRE))->map(fn () => (new EcritureLedgerFactory)->raw([
            'portefeuille_id' => Arr::random($idsPortefeuilles),
            'created_at' => now()->subDays(fake()->numberBetween(0, 365))->toDateTimeString(),
        ]))->all();

        EcritureLedger::insert($ecritures);
    }
}
