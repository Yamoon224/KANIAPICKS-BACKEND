<?php

namespace App\Modules\Paiements\Services;

use App\Models\User;
use App\Modules\Paiements\Contracts\PasserellePaiementContract;
use App\Modules\Paiements\Models\Depot;
use Illuminate\Support\Str;
use Throwable;

/**
 * Initie un dépôt mobile money auprès de l'agrégateur injecté. Le crédit
 * effectif du portefeuille n'a lieu qu'à la confirmation du webhook
 * (cf. ConfirmerDepotAction), jamais ici.
 */
class InitierDepotService
{
    public function __construct(
        private readonly PasserellePaiementContract $passerelle,
    ) {}

    public function initier(User $user, int $montantCents, string $numeroMobileMoney, string $operateur): Depot
    {
        $reference = (string) Str::uuid();

        $depot = Depot::create([
            'user_id' => $user->id,
            'operateur' => $operateur,
            'montant_cents' => $montantCents,
            'statut' => 'initie',
            'reference_agregateur' => $reference,
        ]);

        try {
            $this->passerelle->initier($montantCents, $numeroMobileMoney, $reference);
        } catch (Throwable $exception) {
            $depot->statut = 'echoue';
            $depot->save();

            throw $exception;
        }

        $depot->statut = 'en_attente';
        $depot->save();

        return $depot;
    }
}
