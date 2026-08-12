<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Jeu de données de démonstration : au moins 500 enregistrements par
        // table, dans l'ordre des dépendances (utilisateurs et marchés
        // d'abord, puis tout ce qui les référence).
        $this->call([
            UserSeeder::class,
            MarcheSeeder::class,
            OrdreSeeder::class,
            PositionSeeder::class,
            DepotSeeder::class,
            RetraitSeeder::class,
            EcritureLedgerSeeder::class,
        ]);
    }
}
