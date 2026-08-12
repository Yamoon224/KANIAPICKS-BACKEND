<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Paiements\Models\Retrait;
use Database\Factories\RetraitFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class RetraitSeeder extends Seeder
{
    use WithoutModelEvents;

    public const NOMBRE = 500;

    public function run(): void
    {
        $idsUtilisateurs = User::query()->pluck('id')->all();

        if ($idsUtilisateurs === []) {
            $this->command?->warn('RetraitSeeder ignoré : aucun utilisateur en base.');

            return;
        }

        $maintenant = now()->toDateTimeString();

        $retraits = collect(range(1, self::NOMBRE))->map(fn () => (new RetraitFactory)->raw([
            'user_id' => Arr::random($idsUtilisateurs),
            'created_at' => $maintenant,
            'updated_at' => $maintenant,
        ]))->all();

        Retrait::insert($retraits);
    }
}
