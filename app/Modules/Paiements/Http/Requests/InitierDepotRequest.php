<?php

namespace App\Modules\Paiements\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitierDepotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'montant_cents' => ['required', 'integer', 'min:100'],
            'numero_mobile_money' => ['required', 'string', 'max:20'],
            'operateur' => ['required', 'string', 'in:orange_money,mtn_momo,moov_money,wave,mpesa,airtel_money'],
        ];
    }
}
