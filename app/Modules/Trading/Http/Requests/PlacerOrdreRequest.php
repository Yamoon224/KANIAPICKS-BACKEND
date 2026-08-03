<?php

namespace App\Modules\Trading\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlacerOrdreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'issue' => ['required', 'string', 'in:oui,non'],
            'sens' => ['required', 'string', 'in:achat,vente'],
            'quantite' => ['required', 'integer', 'min:1'],
            'slippage_max_pct' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}
