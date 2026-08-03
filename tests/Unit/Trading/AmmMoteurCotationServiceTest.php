<?php

namespace Tests\Unit\Trading;

use App\Modules\Marches\Models\Marche;
use App\Modules\Trading\Services\AmmMoteurCotationService;
use Database\Factories\MarcheFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AmmMoteurCotationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_prix_initial_est_de_50_pourcent_sur_un_marche_equilibre(): void
    {
        $marche = (new MarcheFactory)->create();
        $moteur = new AmmMoteurCotationService;

        $this->assertSame(50_000, $moteur->prixActuel($marche, 'oui'));
        $this->assertSame(50_000, $moteur->prixActuel($marche, 'non'));
    }

    public function test_acheter_des_parts_oui_augmente_leur_prix_et_reduit_celui_de_non(): void
    {
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();
        $moteur = new AmmMoteurCotationService;

        $coutCents = $moteur->executer($marche, 'oui', 'achat', 100, slippageMaxPct: 100);

        $marche->refresh();
        $this->assertGreaterThan(0, $coutCents);
        $this->assertGreaterThan(50_000, $moteur->prixActuel($marche, 'oui'));
        $this->assertLessThan(50_000, $moteur->prixActuel($marche, 'non'));
        $this->assertSame(100, $marche->q_oui);
    }

    public function test_les_prix_des_deux_issues_somment_toujours_a_la_valeur_nominale(): void
    {
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();
        $moteur = new AmmMoteurCotationService;

        $moteur->executer($marche, 'oui', 'achat', 250, slippageMaxPct: 100);
        $marche->refresh();

        $somme = $moteur->prixActuel($marche, 'oui') + $moteur->prixActuel($marche, 'non');

        $this->assertEqualsWithDelta($marche->valeur_nominale_cents, $somme, 1);
    }

    public function test_vendre_les_parts_achetees_ramene_le_prix_pres_de_son_niveau_initial(): void
    {
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();
        $moteur = new AmmMoteurCotationService;

        $moteur->executer($marche, 'oui', 'achat', 100, slippageMaxPct: 100);
        $marche->refresh();
        $moteur->executer($marche, 'oui', 'vente', 100, slippageMaxPct: 100);
        $marche->refresh();

        $this->assertSame(0, $marche->q_oui);
        $this->assertSame(50_000, $moteur->prixActuel($marche, 'oui'));
    }
}
