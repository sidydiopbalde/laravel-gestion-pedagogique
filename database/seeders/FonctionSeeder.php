<?php

namespace Database\Seeders;

use App\Models\Fonction;
use Illuminate\Database\Seeder;

class FonctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer les rôles prédéfinis
        $fonctions = ['Admin', 'Coach', 'Manager', 'Apprenant', 'CM'];

        foreach ($fonctions as $fonction) {
            Fonction::create([
                'libelle' => $fonction,
            ]);
        }
    }
}

