<?php

namespace App\Http\Resources;

use App\Modules\Trading\Models\Ordre;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ordre */
class OrdreResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'marche_id' => $this->marche_id,
            'sens' => $this->sens,
            'issue' => $this->issue,
            'quantite' => $this->quantite,
            'prix_cents' => $this->prix_cents,
            'frais_cents' => $this->frais_cents,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
