<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;

interface FirebaseServiceInterface  
{
    // Obtenir l'instance de la base de données Firebase
    public function getDatabase(): Database;

    // Stocker de nouvelles données (généralement utilisé avec des requêtes)
    public function store($request);

    // Créer un nouvel enregistrement dans Firebase
    public function create($path, $data);

    // Rechercher un enregistrement spécifique dans Firebase
    public function find($path, $id);

    // Mettre à jour un enregistrement spécifique dans Firebase
    public function update($path, $id, $data);

    // Supprimer un enregistrement spécifique dans Firebase
    public function delete($path, $id);
}
