<?php

namespace App\Modules\Paiements\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Paiements\Actions\ConfirmerDepotAction;
use App\Modules\Paiements\Contracts\PasserellePaiementContract;
use App\Modules\Paiements\Models\Depot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Point d'entrée des notifications de paiement CinetPay. Le corps du
 * webhook n'est jamais utilisé comme source de vérité : le statut est
 * re-vérifié via un appel serveur-à-serveur à l'agrégateur avant tout
 * crédit, ce qui neutralise un webhook falsifié et rend l'endpoint
 * idempotent (cf. cahier des charges, section 8.1).
 */
class CinetPayWebhookController extends Controller
{
    public function __construct(
        private readonly PasserellePaiementContract $passerelle,
        private readonly ConfirmerDepotAction $confirmerDepot,
    ) {}

    public function handle(Request $request): Response
    {
        $referenceAgregateur = $request->string('cpm_trans_id')->value()
            ?: $request->string('transaction_id')->value();

        $depot = Depot::where('reference_agregateur', $referenceAgregateur)->first();

        if (! $depot) {
            return response()->noContent(404);
        }

        $statutVerifie = $this->passerelle->verifier($referenceAgregateur);

        $this->confirmerDepot->executer($depot, $statutVerifie);

        return response()->noContent();
    }
}
