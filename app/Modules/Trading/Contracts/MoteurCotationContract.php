<?php

namespace App\Modules\Trading\Contracts;

use App\Modules\Marches\Models\Marche;

/**
 * Contrat commun aux mécanismes de cotation (AMM / carnet d'ordres),
 * substituables l'un par l'autre sans impacter les appelants (LSP).
 */
interface MoteurCotationContract
{
    /** Prix actuel d'une issue, en centimes de la valeur nominale. */
    public function prixActuel(Marche $marche, string $issue): int;

    /**
     * Exécute un ordre et retourne le montant en centimes (toujours positif) :
     * coût pour un achat, produit de la vente pour une vente. Met à jour
     * l'état du marché de façon atomique.
     */
    public function executer(Marche $marche, string $issue, string $sens, int $quantite, int $slippageMaxPct): int;
}
