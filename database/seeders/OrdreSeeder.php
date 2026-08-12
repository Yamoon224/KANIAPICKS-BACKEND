<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Marches\Models\Marche;
use App\Modules\Trading\Models\Ordre;
use Database\Factories\OrdreFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class OrdreSeeder extends Seeder
{
    use WithoutModelEvents;

    public const NOMBRE = 500;

    public function run(): void
    {
        $idsUtilisateurs = User::query()->pluck('id')->all();
        $idsMarches = Marche::query()->pluck('id')->all();

        if ($idsUtilisateurs === [] || $idsMarches === []) {
            $this->command?->warn('OrdreSeeder ignoré : aucun utilisateur ou marché en base.');

            return;
        }

        $maintenant = now()->toDateTimeString();

        $ordres = collect(range(1, self::NOMBRE))->map(fn () => (new OrdreFactory)->raw([
            'user_id' => Arr::random($idsUtilisateurs),
            'marche_id' => Arr::random($idsMarches),
            'created_at' => $maintenant,
            'updated_at' => $maintenant,
        ]))->all();

        Ordre::insert($ordres);
    }
}
