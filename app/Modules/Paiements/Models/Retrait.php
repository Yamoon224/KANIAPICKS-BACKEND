<?php

namespace App\Modules\Paiements\Models;

use Illuminate\Database\Eloquent\Model;

class Retrait extends Model
{
    protected $table = 'retraits';

    protected $fillable = [
        'user_id',
        'operateur',
        'numero_destinataire',
        'montant_cents',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'montant_cents' => 'integer',
        ];
    }
}
