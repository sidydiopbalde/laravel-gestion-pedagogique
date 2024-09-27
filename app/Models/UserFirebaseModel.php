<?php

namespace App\Models;

use App\Models\FirebaseModel;

class UserFirebaseModel extends FirebaseModel
{
    protected $path = 'users'; 

    protected $fillable = ['nom', 'prenom', 'adresse', 'telephone', 'fonction_id', 'email', 'photo', 'statut','firebase_uid'];

      public function scopeFonction($query, $fonctionId)
      {
          return $query->where('fonction_id', $fonctionId);
      }
}



