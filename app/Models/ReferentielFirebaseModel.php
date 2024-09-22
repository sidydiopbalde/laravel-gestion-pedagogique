<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferentielFirebaseModel extends FirebaseModel
{
    use HasFactory;
    protected $fillable = [
        'libelle',
        'description',
        'statut',
    ];
    protected $table ='referentiels';
    protected $casts = [
        'statut' => 'boolean',
    ];

}
