<?php

namespace App\Modules\Trading\Exceptions;

use RuntimeException;

class SlippageDepasseException extends RuntimeException
{
    public function __construct(int $prixAvantCents, int $prixMoyenExecuteCents)
    {
        parent::__construct(
            "Slippage maximal dépassé : prix affiché {$prixAvantCents}, prix moyen exécuté {$prixMoyenExecuteCents}."
        );
    }
}
