<?php

namespace App\Http\Resources;

use App\Modules\Marches\Models\Marche;
use App\Modules\Trading\Contracts\MoteurCotationContract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Marche */
class MarcheResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $moteurCotation = app(MoteurCotationContract::class);

        return [
            'id' => $this->id,
            'question' => $this->question,
            'categorie' => $this->categorie,
            'statut' => $this->statut,
            'regle_resolution' => $this->regle_resolution,
            'source_officielle' => $this->source_officielle,
            'echeance_at' => $this->echeance_at?->toIso8601String(),
            'valeur_nominale_cents' => $this->valeur_nominale_cents,
            'prix_oui_cents' => $moteurCotation->prixActuel($this->resource, 'oui'),
            'prix_non_cents' => $moteurCotation->prixActuel($this->resource, 'non'),
            'issue_gagnante' => $this->issue_gagnante,
            'preuve_url' => $this->preuve_url,
            'resolu_at' => $this->resolu_at?->toIso8601String(),
        ];
    }
}
