<?php

namespace App\Modules\Paiements\Contracts;

/**
 * Contrat commun à tous les agrégateurs de paiement (CinetPay, PayDunya,
 * Flutterwave...). Chaque implémentation est interchangeable sans effet de
 * bord (LSP) ; le moteur métier ne dépend que de ce contrat, jamais d'un
 * agrégateur concret (DIP).
 */
interface PasserellePaiementContract
{
    public function initier(int $montantCents, string $numeroMobileMoney, string $reference): string;

    public function verifier(string $referenceAgregateur): string;

    public function rembourser(string $referenceAgregateur, int $montantCents): void;

    public function verser(int $montantCents, string $numeroMobileMoney, string $reference): string;
}
