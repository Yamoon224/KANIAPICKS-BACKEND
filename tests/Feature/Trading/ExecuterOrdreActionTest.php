<?php

namespace Tests\Feature\Trading;

use App\Modules\Marches\Models\Marche;
use App\Modules\Portefeuille\Exceptions\SoldeInsuffisantException;
use App\Modules\Portefeuille\Models\EcritureLedger;
use App\Modules\Trading\Actions\ExecuterOrdreAction;
use App\Modules\Trading\Exceptions\PositionInsuffisanteException;
use App\Modules\Trading\Models\Position;
use Database\Factories\MarcheFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreeUtilisateurAvecPortefeuille;
use Tests\TestCase;

class ExecuterOrdreActionTest extends TestCase
{
    use CreeUtilisateurAvecPortefeuille;
    use RefreshDatabase;

    public function test_un_achat_debite_lacheteur_et_cree_une_position(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(1_000_000_00);
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();

        $ordre = app(ExecuterOrdreAction::class)->executer($user, $marche, 'oui', 'achat', 100, 100);

        $this->assertSame(100, $ordre->quantite);
        $this->assertLessThan(1_000_000_00, $user->portefeuille->fresh()->solde_disponible_cents);

        $position = Position::where('user_id', $user->id)->where('marche_id', $marche->id)->where('issue', 'oui')->first();
        $this->assertSame(100, $position->quantite);
    }

    public function test_toute_transaction_de_trading_conserve_largent_dans_le_ledger(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(1_000_000_00);
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();

        app(ExecuterOrdreAction::class)->executer($user, $marche, 'oui', 'achat', 100, 100);

        $sommeMouvements = EcritureLedger::sum('montant_cents');

        $this->assertSame(0, (int) $sommeMouvements);
    }

    public function test_un_solde_insuffisant_annule_integralement_lordre_y_compris_letat_de_lamm(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(1_00);
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();

        try {
            app(ExecuterOrdreAction::class)->executer($user, $marche, 'oui', 'achat', 1_000_000, 100);
            $this->fail('SoldeInsuffisantException attendue.');
        } catch (SoldeInsuffisantException) {
            // attendu
        }

        $this->assertSame(1_00, $user->portefeuille->fresh()->solde_disponible_cents);
        $this->assertSame(0, $marche->fresh()->q_oui);
        $this->assertSame(0, EcritureLedger::count());
    }

    public function test_vendre_plus_que_sa_position_est_refuse(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(1_000_000_00);
        /** @var Marche $marche */
        $marche = (new MarcheFactory)->create();

        $this->expectException(PositionInsuffisanteException::class);

        app(ExecuterOrdreAction::class)->executer($user, $marche, 'oui', 'vente', 10, 100);
    }
}
