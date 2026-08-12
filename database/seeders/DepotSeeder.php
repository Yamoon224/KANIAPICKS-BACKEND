<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Paiements\Models\Depot;
use Database\Factories\DepotFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DepotSeeder extends Seeder
{
    use WithoutModelEvents;

    public const NOMBRE = 500;

    public function run(): void
    {
        $idsUtilisateurs = User::query()->pluck('id')->all();

        if ($idsUtilisateurs === []) {
            $this->command?->warn('DepotSeeder ignoré : aucun utilisateur en base.');

            return;
        }

        $maintenant = now()->toDateTimeString();

        $depots = collect(range(1, self::NOMBRE))->map(fn () => (new DepotFactory)->raw([
            'user_id' => Arr::random($idsUtilisateurs),
            'created_at' => $maintenant,
            'updated_at' => $maintenant,
        ]))->all();

        Depot::insert($depots);
    }
}
