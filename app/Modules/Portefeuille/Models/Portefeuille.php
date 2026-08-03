<?php

namespace App\Modules\Portefeuille\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Portefeuille extends Model
{
    protected $table = 'portefeuilles';

    protected $fillable = [
        'user_id',
        'devise',
        'solde_disponible_cents',
        'solde_engage_cents',
        'solde_en_attente_retrait_cents',
        'solde_bonus_cents',
    ];

    protected function casts(): array
    {
        return [
            'solde_disponible_cents' => 'integer',
            'solde_engage_cents' => 'integer',
            'solde_en_attente_retrait_cents' => 'integer',
            'solde_bonus_cents' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ecritures(): HasMany
    {
        return $this->hasMany(EcritureLedger::class, 'portefeuille_id');
    }

    /**
     * Contrepartie de tous les mouvements du teneur de marché automatisé
     * (liquidité AMM) et des frais de trading — permet au ledger de rester
     * en partie double : aucun montant ne disparaît ni n'apparaît du néant.
     */
    public static function compteplateforme(): self
    {
        $email = config('kaniapicks.email_compte_plateforme');

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Compte plateforme', 'password' => bcrypt(Str::random(40))],
        );

        return static::firstOrCreate(
            ['user_id' => $user->id],
            ['devise' => config('kaniapicks.devise_defaut')],
        );
    }
}
