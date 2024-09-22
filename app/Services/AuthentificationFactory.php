<?php
namespace App\Services;

use App\Http\Controllers\FirebaseAuthController;
use App\Services\AuthentificationPassport;
use App\Services\AuthentificationFirebase;
use Kreait\Firebase\Auth as FirebaseAuth;

class AuthentificationFactory
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function make(): AuthentificationServiceInterface
    {
        // Lire la variable d'environnement AUTH_TYPE
        $authType = env('AUTH_TYPE', 'passport'); // Par défaut 'passport'
        
        if ($authType === 'firebase') {
            // Si l'authentification Firebase est sélectionnée
            return new FirebaseAuthController(new FirebaseService());
        }
    
        // Par défaut, on retourne l'authentification Passport
        return new AuthentificationPassport();
    }
}
