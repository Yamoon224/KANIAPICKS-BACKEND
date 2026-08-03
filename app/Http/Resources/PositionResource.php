<?php

namespace App\Http\Resources;

use App\Modules\Trading\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Position */
class PositionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'marche_id' => $this->marche_id,
            'issue' => $this->issue,
            'quantite' => $this->quantite,
            'prix_revient_total_cents' => $this->prix_revient_total_cents,
            'prix_revient_moyen_cents' => $this->quantite > 0
                ? intdiv($this->prix_revient_total_cents, $this->quantite)
                : 0,
        ];
    }
}
