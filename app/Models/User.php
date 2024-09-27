<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User  extends Authenticatable
{
    use HasFactory,  HasApiTokens,Notifiable;
    // use Synchwithfirebase;                                                                                                                                                                                                                                                                                                                                                                   
    protected $fillable = [
        'nom', 'prenom', 'adresse', 'telephone', 'fonction_id', 'email', 'photo', 'statut','password','firebase_uid'
    ];

    // Relation avec Fonction
    public function fonction()
    {
        return $this->belongsTo(Fonction::class);
    }
}

