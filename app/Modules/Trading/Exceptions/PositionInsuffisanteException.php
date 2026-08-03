<?php

namespace App\Modules\Trading\Exceptions;

use RuntimeException;

class PositionInsuffisanteException extends RuntimeException
{
    public function __construct(int $quantiteDetenue, int $quantiteDemandee)
    {
        parent::__construct(
            "Position insuffisante : détenue {$quantiteDetenue}, demandée {$quantiteDemandee}."
        );
    }
}
