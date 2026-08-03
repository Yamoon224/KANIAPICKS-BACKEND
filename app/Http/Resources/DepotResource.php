<?php

namespace App\Http\Resources;

use App\Modules\Paiements\Models\Depot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Depot */
class DepotResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'operateur' => $this->operateur,
            'montant_cents' => $this->montant_cents,
            'statut' => $this->statut,
            'reference_agregateur' => $this->reference_agregateur,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
