<?php

namespace App\Models;

use App\Models\FirebaseModel;

class UserFirebaseModel extends FirebaseModel
{
    protected $path = 'users'; // Chemin dans Firebase

    protected $fillable = ['nom', 'prenom', 'adresse', 'telephone', 'fonction_id', 'email', 'photo', 'statut'];

      public function scopeFonction($query, $fonctionId)
      {
          return $query->where('fonction_id', $fonctionId);
      }
}



