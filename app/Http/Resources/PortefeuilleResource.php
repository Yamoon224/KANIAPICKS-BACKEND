<?php

namespace App\Http\Resources;

use App\Modules\Portefeuille\Models\Portefeuille;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Portefeuille */
class PortefeuilleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'devise' => $this->devise,
            'solde_disponible_cents' => $this->solde_disponible_cents,
            'solde_engage_cents' => $this->solde_engage_cents,
            'solde_en_attente_retrait_cents' => $this->solde_en_attente_retrait_cents,
            'solde_bonus_cents' => $this->solde_bonus_cents,
        ];
    }
}
