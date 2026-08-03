<?php

namespace App\Modules\Marches\Models;

use App\Modules\Trading\Models\Ordre;
use App\Modules\Trading\Models\Position;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marche extends Model
{
    protected $table = 'marches';

    protected $fillable = [
        'question',
        'categorie',
        'statut',
        'regle_resolution',
        'source_officielle',
        'echeance_at',
        'valeur_nominale_cents',
        'q_oui',
        'q_non',
        'liquidite_b',
        'issue_gagnante',
        'preuve_url',
        'resolu_at',
    ];

    protected function casts(): array
    {
        return [
            'echeance_at' => 'datetime',
            'resolu_at' => 'datetime',
            'valeur_nominale_cents' => 'integer',
            'q_oui' => 'integer',
            'q_non' => 'integer',
            'liquidite_b' => 'integer',
        ];
    }

    public function ordres(): HasMany
    {
        return $this->hasMany(Ordre::class, 'marche_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'marche_id');
    }
}
