<?php

namespace App\Modules\Trading\Models;

use App\Models\User;
use App\Modules\Marches\Models\Marche;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ordre extends Model
{
    protected $table = 'ordres';

    protected $fillable = [
        'user_id',
        'marche_id',
        'sens',
        'issue',
        'quantite',
        'prix_cents',
        'frais_cents',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
            'prix_cents' => 'integer',
            'frais_cents' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marche(): BelongsTo
    {
        return $this->belongsTo(Marche::class, 'marche_id');
    }
}
