<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Conformite\Models\ProfilKyc;
use App\Modules\Portefeuille\Models\Portefeuille;
use Database\Factories\ProfilKycFactory;
use Database\Factories\PortefeuilleFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Crée des comptes traders avec leur portefeuille et leur profil KYC, comme
 * le fait AuthController::register() à l'inscription.
 */
class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public const NOMBRE = 500;

    // Pondérée très majoritairement vers "trader" ; quelques comptes des
    // autres rôles pour pouvoir tester les fonctionnalités qui en dépendent
    // (publication de marché, résolution, support, etc.).
    private const ROLES = [
        'trader', 'trader', 'trader', 'trader', 'trader', 'trader', 'trader', 'trader',
        'editeur', 'agent_resolution', 'agent_support', 'tresorier', 'admin',
    ];

    public function run(): void
    {
        $utilisateurs = User::factory()
            ->count(self::NOMBRE)
            ->state(fn () => ['role' => Arr::random(self::ROLES)])
            ->create();

        $maintenant = now()->toDateTimeString();

        $portefeuilles = $utilisateurs->map(fn (User $utilisateur) => (new PortefeuilleFactory)->raw([
            'user_id' => $utilisateur->id,
            'created_at' => $maintenant,
            'updated_at' => $maintenant,
        ]))->all();

        Portefeuille::insert($portefeuilles);

        $profilsKyc = $utilisateurs->map(fn (User $utilisateur) => (new ProfilKycFactory)->raw([
            'user_id' => $utilisateur->id,
            'created_at' => $maintenant,
            'updated_at' => $maintenant,
        ]))->all();

        ProfilKyc::insert($profilsKyc);
    }
}
