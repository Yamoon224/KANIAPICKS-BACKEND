<?php

namespace App\Modules\Marches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarcheRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:500'],
            'categorie' => ['required', 'string', 'in:football_can,autres_sports,politique_elections,economie_fcfa,musique_divertissement,actualite_africaine,international,meteo_divers'],
            'regle_resolution' => ['required', 'string'],
            'source_officielle' => ['required', 'string', 'max:255'],
            'echeance_at' => ['required', 'date', 'after:now'],
            'valeur_nominale_cents' => ['sometimes', 'integer', 'min:1'],
            'liquidite_b' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
