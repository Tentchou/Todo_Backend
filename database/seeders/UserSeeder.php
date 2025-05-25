<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'], // Cherche par email pour éviter les doublons
            [
                'name' => 'Test User',
                'password' => Hash::make('password'), // Mot de passe facile pour le test
                'email_verified_at' => now(), // Simule un email vérifié
            ]
        );

        User::factory(5)->create(); // Crée 5 utilisateurs supplémentaires avec des données aléatoires
    }
}
