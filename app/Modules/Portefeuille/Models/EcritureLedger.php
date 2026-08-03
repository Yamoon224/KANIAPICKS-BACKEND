<?php

namespace App\Modules\Portefeuille\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Écriture immuable du grand livre comptable (partie double). Aucune
 * écriture n'est jamais modifiée ni supprimée après création (append-only).
 */
class EcritureLedger extends Model
{
    protected $table = 'ecritures_ledger';

    public $timestamps = false;

    protected $fillable = [
        'portefeuille_id',
        'type',
        'montant_cents',
        'solde_apres_cents',
        'reference',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'montant_cents' => 'integer',
            'solde_apres_cents' => 'integer',
            'created_at' => 'datetime',
        ];
    }
}
