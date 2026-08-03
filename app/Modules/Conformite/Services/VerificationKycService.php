<?php

namespace App\Modules\Conformite\Services;

use App\Modules\Conformite\Models\ProfilKyc;

/**
 * Vérification automatisée des pièces (OCR + détection de vivacité) avec
 * mise en file d'attente pour revue manuelle en back-office des cas douteux.
 */
class VerificationKycService
{
    public function soumettre(int $userId, int $palierCible, array $documents): ProfilKyc
    {
        throw new \RuntimeException('Not implemented.');
    }
}
