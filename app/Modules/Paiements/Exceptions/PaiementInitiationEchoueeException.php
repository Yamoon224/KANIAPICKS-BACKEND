<?php

namespace App\Modules\Paiements\Exceptions;

use RuntimeException;

class PaiementInitiationEchoueeException extends RuntimeException
{
    public function __construct(string $reponseAgregateur)
    {
        parent::__construct("Échec de l'initiation du paiement auprès de l'agrégateur : {$reponseAgregateur}");
    }
}
