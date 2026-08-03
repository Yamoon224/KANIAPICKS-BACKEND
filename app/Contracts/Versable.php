<?php

namespace App\Contracts;

interface Versable
{
    public function debiter(int $portefeuilleId, int $montantCents, string $motif, ?string $reference = null): void;
}
