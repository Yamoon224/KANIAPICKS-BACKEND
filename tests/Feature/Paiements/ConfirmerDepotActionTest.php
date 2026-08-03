<?php

namespace Tests\Feature\Paiements;

use App\Modules\Paiements\Actions\ConfirmerDepotAction;
use App\Modules\Paiements\Models\Depot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreeUtilisateurAvecPortefeuille;
use Tests\TestCase;

class ConfirmerDepotActionTest extends TestCase
{
    use CreeUtilisateurAvecPortefeuille;
    use RefreshDatabase;

    public function test_un_depot_confirme_credite_le_portefeuille_une_seule_fois_meme_rejoue(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(0);
        $depot = Depot::create([
            'user_id' => $user->id,
            'operateur' => 'orange_money',
            'montant_cents' => 500_00,
            'statut' => 'en_attente',
            'reference_agregateur' => 'ref-test-1',
        ]);

        $action = app(ConfirmerDepotAction::class);

        $action->executer($depot, 'confirme');
        $action->executer($depot->fresh(), 'confirme');
        $action->executer($depot->fresh(), 'confirme');

        $this->assertSame(500_00, $user->portefeuille->fresh()->solde_disponible_cents);
        $this->assertSame('confirme', $depot->fresh()->statut);
        $this->assertSame(
            1,
            $user->portefeuille->fresh()->ecritures()->where('type', 'depot')->count(),
        );
    }

    public function test_un_depot_echoue_ne_credite_jamais_le_portefeuille(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(0);
        $depot = Depot::create([
            'user_id' => $user->id,
            'operateur' => 'orange_money',
            'montant_cents' => 500_00,
            'statut' => 'en_attente',
            'reference_agregateur' => 'ref-test-2',
        ]);

        app(ConfirmerDepotAction::class)->executer($depot, 'echoue');

        $this->assertSame(0, $user->portefeuille->fresh()->solde_disponible_cents);
        $this->assertSame('echoue', $depot->fresh()->statut);
    }
}
