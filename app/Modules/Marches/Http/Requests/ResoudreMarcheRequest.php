<?php

namespace App\Modules\Marches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResoudreMarcheRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'issue_gagnante' => ['required', 'string', 'in:oui,non'],
            'preuve_url' => ['required', 'string', 'url'],
        ];
    }
}
