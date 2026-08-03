<?php

namespace Tests\Concerns;

use App\Models\User;
use App\Modules\Portefeuille\Models\Portefeuille;

trait CreeUtilisateurAvecPortefeuille
{
    protected function creerUtilisateurAvecPortefeuille(int $soldeInitialCents = 0, array $attributs = []): User
    {
        $user = User::factory()->create($attributs);

        Portefeuille::create([
            'user_id' => $user->id,
            'devise' => 'XOF',
            'solde_disponible_cents' => $soldeInitialCents,
        ]);

        return $user->refresh();
    }
}
