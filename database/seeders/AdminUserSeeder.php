<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Supprimer l'ancien admin s'il existe
        User::where('email', 'admin@grossiste-ouaga.bf')->delete();
        
        // Créer le nouvel admin avec l'email de l'entreprise
        User::firstOrCreate(
            ['email' => 'grossisteouagainternational@gmail.com'],
            [
                'name' => 'Admin Grossiste Ouaga International',
                'password' => Hash::make('Admin@2025!'), // Mot de passe plus sécurisé
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Utilisateur admin créé avec succès !');
        $this->command->info('📧 Email : grossisteouagainternational@gmail.com');
        $this->command->info('🔑 Mot de passe : Admin@2025!');
    }
}
