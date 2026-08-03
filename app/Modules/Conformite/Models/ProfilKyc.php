<?php

namespace App\Modules\Conformite\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilKyc extends Model
{
    protected $table = 'profils_kyc';

    protected $fillable = [
        'user_id',
        'palier',
        'statut',
        'plafond_depot_journalier_cents',
        'plafond_retrait_journalier_cents',
    ];

    protected function casts(): array
    {
        return [
            'palier' => 'integer',
            'plafond_depot_journalier_cents' => 'integer',
            'plafond_retrait_journalier_cents' => 'integer',
        ];
    }
}
