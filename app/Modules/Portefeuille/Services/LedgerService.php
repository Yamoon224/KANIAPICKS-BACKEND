<?php

namespace App\Modules\Portefeuille\Services;

use App\Contracts\Encaissable;
use App\Contracts\Versable;
use App\Modules\Portefeuille\Exceptions\SoldeInsuffisantException;
use App\Modules\Portefeuille\Models\EcritureLedger;
use App\Modules\Portefeuille\Models\Portefeuille;
use Illuminate\Support\Facades\DB;

/**
 * Seul point d'entrée pour modifier le solde disponible d'un portefeuille.
 * Chaque mouvement s'exécute dans une transaction avec verrouillage de ligne
 * (SELECT ... FOR UPDATE) et génère une écriture de ledger immuable — aucun
 * solde n'est jamais modifié directement ailleurs dans le code (cf. cahier
 * des charges, section 5.4).
 */
class LedgerService implements Encaissable, Versable
{
    public function crediter(int $portefeuilleId, int $montantCents, string $motif, ?string $reference = null): void
    {
        if ($montantCents <= 0) {
            throw new \InvalidArgumentException('Le montant à créditer doit être positif.');
        }

        DB::transaction(function () use ($portefeuilleId, $montantCents, $motif, $reference) {
            $portefeuille = Portefeuille::whereKey($portefeuilleId)->lockForUpdate()->firstOrFail();

            $portefeuille->solde_disponible_cents += $montantCents;
            $portefeuille->save();

            EcritureLedger::create([
                'portefeuille_id' => $portefeuille->id,
                'type' => $motif,
                'montant_cents' => $montantCents,
                'solde_apres_cents' => $portefeuille->solde_disponible_cents,
                'reference' => $reference,
                'created_at' => now(),
            ]);
        });
    }

    public function debiter(int $portefeuilleId, int $montantCents, string $motif, ?string $reference = null): void
    {
        if ($montantCents <= 0) {
            throw new \InvalidArgumentException('Le montant à débiter doit être positif.');
        }

        DB::transaction(function () use ($portefeuilleId, $montantCents, $motif, $reference) {
            $portefeuille = Portefeuille::whereKey($portefeuilleId)->lockForUpdate()->firstOrFail();

            if ($portefeuille->solde_disponible_cents < $montantCents) {
                throw new SoldeInsuffisantException($portefeuille->solde_disponible_cents, $montantCents);
            }

            $portefeuille->solde_disponible_cents -= $montantCents;
            $portefeuille->save();

            EcritureLedger::create([
                'portefeuille_id' => $portefeuille->id,
                'type' => $motif,
                'montant_cents' => -$montantCents,
                'solde_apres_cents' => $portefeuille->solde_disponible_cents,
                'reference' => $reference,
                'created_at' => now(),
            ]);
        });
    }
}
