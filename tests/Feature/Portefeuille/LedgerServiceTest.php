<?php

namespace Tests\Feature\Portefeuille;

use App\Modules\Portefeuille\Exceptions\SoldeInsuffisantException;
use App\Modules\Portefeuille\Models\EcritureLedger;
use App\Modules\Portefeuille\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreeUtilisateurAvecPortefeuille;
use Tests\TestCase;

class LedgerServiceTest extends TestCase
{
    use CreeUtilisateurAvecPortefeuille;
    use RefreshDatabase;

    public function test_crediter_augmente_le_solde_et_cree_une_ecriture(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(1_000_00);
        $ledger = new LedgerService;

        $ledger->crediter($user->portefeuille->id, 500_00, 'depot');

        $this->assertSame(1_500_00, $user->portefeuille->fresh()->solde_disponible_cents);

        $ecriture = EcritureLedger::where('portefeuille_id', $user->portefeuille->id)->latest('id')->first();
        $this->assertSame(500_00, $ecriture->montant_cents);
        $this->assertSame(1_500_00, $ecriture->solde_apres_cents);
        $this->assertSame('depot', $ecriture->type);
    }

    public function test_debiter_diminue_le_solde_et_cree_une_ecriture_negative(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(1_000_00);
        $ledger = new LedgerService;

        $ledger->debiter($user->portefeuille->id, 300_00, 'retrait');

        $this->assertSame(700_00, $user->portefeuille->fresh()->solde_disponible_cents);

        $ecriture = EcritureLedger::where('portefeuille_id', $user->portefeuille->id)->latest('id')->first();
        $this->assertSame(-300_00, $ecriture->montant_cents);
    }

    public function test_debiter_refuse_un_solde_insuffisant_sans_rien_modifier(): void
    {
        $user = $this->creerUtilisateurAvecPortefeuille(100_00);
        $ledger = new LedgerService;

        $this->expectException(SoldeInsuffisantException::class);

        try {
            $ledger->debiter($user->portefeuille->id, 200_00, 'achat');
        } finally {
            $this->assertSame(100_00, $user->portefeuille->fresh()->solde_disponible_cents);
            $this->assertSame(0, EcritureLedger::where('portefeuille_id', $user->portefeuille->id)->count());
        }
    }
}
