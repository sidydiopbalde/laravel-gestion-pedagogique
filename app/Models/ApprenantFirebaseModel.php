<?php

namespace App\Models;

use App\Models\FirebaseModel;

class ApprenantFirebaseModel extends FirebaseModel
{
    protected $path = 'apprenants';
    protected $fillable = ['nom', 'prenom', 'adresse', 'telephone', 'fonction_id', 'email', 'photo', 'statut'];
    public $email;
    public $matricule;
    public $nom;
    public $prenom;
    public $default_password;

}
