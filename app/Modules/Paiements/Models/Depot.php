<?php

namespace App\Modules\Paiements\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depot extends Model
{
    protected $table = 'depots';

    protected $fillable = [
        'user_id',
        'operateur',
        'montant_cents',
        'statut',
        'reference_agregateur',
    ];

    protected function casts(): array
    {
        return [
            'montant_cents' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
