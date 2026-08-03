<?php

namespace App\Modules\Paiements\Actions;

use App\Contracts\Encaissable;
use App\Modules\Paiements\Models\Depot;
use App\Modules\Portefeuille\Models\Portefeuille;
use Illuminate\Support\Facades\DB;

/**
 * Traite la confirmation d'un dépôt (webhook agrégateur). Idempotente : un
 * dépôt déjà confirmé ne peut jamais être crédité une seconde fois, même en
 * cas de webhook rejoué (cf. cahier des charges, section 5.5 — « relance
 * automatique des webhooks manqués »).
 */
class ConfirmerDepotAction
{
    public function __construct(
        private readonly Encaissable $encaissable,
    ) {}

    public function executer(Depot $depot, string $statutAgregateur): void
    {
        DB::transaction(function () use ($depot, $statutAgregateur) {
            $depotVerrouille = Depot::whereKey($depot->id)->lockForUpdate()->firstOrFail();

            if (in_array($depotVerrouille->statut, ['confirme', 'rembourse'], true)) {
                return;
            }

            if ($statutAgregateur !== 'confirme') {
                $depotVerrouille->statut = $statutAgregateur;
                $depotVerrouille->save();

                return;
            }

            $portefeuille = Portefeuille::where('user_id', $depotVerrouille->user_id)->firstOrFail();

            $this->encaissable->crediter(
                $portefeuille->id,
                $depotVerrouille->montant_cents,
                'depot',
                "depot-{$depotVerrouille->id}",
            );

            $depotVerrouille->statut = 'confirme';
            $depotVerrouille->save();
        });
    }
}
