<?php

namespace App\Modules\Portefeuille\Exceptions;

use RuntimeException;

class SoldeInsuffisantException extends RuntimeException
{
    public function __construct(int $soldeDisponibleCents, int $montantDemandeCents)
    {
        parent::__construct(
            "Solde insuffisant : disponible {$soldeDisponibleCents}, demandé {$montantDemandeCents}."
        );
    }
}
