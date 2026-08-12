<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Marches\Models\Marche;
use App\Modules\Trading\Models\Position;
use Database\Factories\PositionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PositionSeeder extends Seeder
{
    use WithoutModelEvents;

    public const NOMBRE = 500;

    private const ISSUES = ['oui', 'non'];

    public function run(): void
    {
        $idsUtilisateurs = User::query()->pluck('id')->all();
        $idsMarches = Marche::query()->pluck('id')->all();

        if ($idsUtilisateurs === [] || $idsMarches === []) {
            $this->command?->warn('PositionSeeder ignoré : aucun utilisateur ou marché en base.');

            return;
        }

        $maintenant = now()->toDateTimeString();

        // La table impose l'unicité de (user_id, marche_id, issue) : on
        // tire des combinaisons au hasard jusqu'à en obtenir 500 distinctes,
        // plutôt que de risquer une violation de contrainte à l'insertion.
        $dejaVues = [];
        $lignes = [];
        $tentativesMax = self::NOMBRE * 20;

        for ($tentative = 0; count($lignes) < self::NOMBRE && $tentative < $tentativesMax; $tentative++) {
            $userId = Arr::random($idsUtilisateurs);
            $marcheId = Arr::random($idsMarches);
            $issue = Arr::random(self::ISSUES);
            $cle = "{$userId}:{$marcheId}:{$issue}";

            if (isset($dejaVues[$cle])) {
                continue;
            }
            $dejaVues[$cle] = true;

            $lignes[] = (new PositionFactory)->raw([
                'user_id' => $userId,
                'marche_id' => $marcheId,
                'issue' => $issue,
                'created_at' => $maintenant,
                'updated_at' => $maintenant,
            ]);
        }

        Position::insert($lignes);
    }
}
