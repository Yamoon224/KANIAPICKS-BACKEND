<?php

namespace Tests\Feature\Marches;

use App\Modules\Marches\Actions\ResoudreMarcheAction;
use App\Modules\Marches\Models\Marche;
use App\Modules\Trading\Actions\ExecuterOrdreAction;
use Database\Factories\MarcheFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreeUtilisateurAvecPortefeuille;
use Tests\TestCase;

class ResoudreMarcheActionTest extends TestCase
{
    use CreeUtilisateurAvecPortefeuille;
    use RefreshDatabase;

    public function test_la_resolution_paie_les_positions_gagnantes_et_ignore_les_perdantes(): void
    {
        $gagnant = $this->creerUtilisateurAvecPortefeuille(1_000_000_00);
        $perdant = $this->creerUtilisateurAvecPortefeuille(1_000_000_00);
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();

        app(ExecuterOrdreAction::class)->executer($gagnant, $marche, 'oui', 'achat', 100, 100);
        app(ExecuterOrdreAction::class)->executer($perdant, $marche, 'non', 'achat', 100, 100);

        $soldeGagnantAvant = $gagnant->portefeuille->fresh()->solde_disponible_cents;
        $soldePerdantAvant = $perdant->portefeuille->fresh()->solde_disponible_cents;

        app(ResoudreMarcheAction::class)->executer($marche, 'oui', 'https://exemple.org/preuve');

        $marche->refresh();
        $this->assertSame('resolu', $marche->statut);
        $this->assertSame('oui', $marche->issue_gagnante);

        $gainAttenduCents = 100 * $marche->valeur_nominale_cents;
        $this->assertSame(
            $soldeGagnantAvant + $gainAttenduCents,
            $gagnant->portefeuille->fresh()->solde_disponible_cents,
        );
        $this->assertSame($soldePerdantAvant, $perdant->portefeuille->fresh()->solde_disponible_cents);
    }

    public function test_resoudre_un_marche_deja_resolu_est_refuse(): void
    {
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create(['statut' => 'resolu']);

        $this->expectException(\RuntimeException::class);

        app(ResoudreMarcheAction::class)->executer($marche, 'oui', 'https://exemple.org/preuve');
    }
}
