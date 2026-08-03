<?php

namespace App\Contracts;

interface Encaissable
{
    public function crediter(int $portefeuilleId, int $montantCents, string $motif, ?string $reference = null): void;
}
