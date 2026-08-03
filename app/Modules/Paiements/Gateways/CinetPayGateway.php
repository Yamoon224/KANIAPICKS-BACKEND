<?php

namespace App\Modules\Paiements\Gateways;

use App\Modules\Paiements\Contracts\PasserellePaiementContract;
use App\Modules\Paiements\Exceptions\PaiementInitiationEchoueeException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Intégration CinetPay (API Checkout v2). initier() et verifier() suivent
 * la forme documentée de l'API CinetPay ; à valider en sandbox avec de
 * vraies clés avant mise en production. rembourser()/verser() n'ont pas
 * d'équivalent API standard chez cet agrégateur et doivent être finalisés
 * au cadrage (back-office manuel ou API de transfert dédiée).
 */
class CinetPayGateway implements PasserellePaiementContract
{
    public function initier(int $montantCents, string $numeroMobileMoney, string $reference): string
    {
        $response = Http::asJson()->post(config('services.cinetpay.base_url').'/payment', [
            'apikey' => config('services.cinetpay.api_key'),
            'site_id' => config('services.cinetpay.site_id'),
            'transaction_id' => $reference,
            'amount' => intdiv($montantCents, 100),
            'currency' => config('kaniapicks.devise_defaut'),
            'description' => 'Dépôt KANIAPICKS',
            'customer_phone_number' => $numeroMobileMoney,
            'notify_url' => config('services.cinetpay.notify_url'),
            'channels' => 'MOBILE_MONEY',
        ]);

        if ($response->failed() || (string) $response->json('code') !== '201') {
            throw new PaiementInitiationEchoueeException($response->body());
        }

        return (string) ($response->json('data.payment_token') ?? $reference);
    }

    public function verifier(string $referenceAgregateur): string
    {
        $response = Http::asJson()->post(config('services.cinetpay.base_url').'/payment/check', [
            'apikey' => config('services.cinetpay.api_key'),
            'site_id' => config('services.cinetpay.site_id'),
            'transaction_id' => $referenceAgregateur,
        ]);

        return match ($response->json('data.status')) {
            'ACCEPTED' => 'confirme',
            'REFUSED', 'CANCELLED' => 'echoue',
            default => 'en_attente',
        };
    }

    public function rembourser(string $referenceAgregateur, int $montantCents): void
    {
        throw new RuntimeException(
            "CinetPay n'expose pas d'API de remboursement standard : traiter manuellement en back-office, ".
            'puis créditer le portefeuille via le module Portefeuille.',
        );
    }

    public function verser(int $montantCents, string $numeroMobileMoney, string $reference): string
    {
        throw new RuntimeException(
            "L'API de transfert (payout) CinetPay nécessite un compte marchand dédié, à intégrer lors du ".
            'cadrage des retraits mobile money.',
        );
    }
}
