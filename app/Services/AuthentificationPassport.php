<?php
namespace App\Services;

use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthentificationServiceInterface;
class AuthentificationPassport implements AuthentificationServiceInterface
{
    
    public function authenticate(array $credentials)
    {
        // Valider les informations d'identification fournies
        $validator = Validator::make($credentials, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Identifier si l'utilisateur se connecte avec un nom d'utilisateur ou un email
        $loginType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Tentative de connexion avec les informations d'identification fournies
        if (Auth::attempt([$loginType => $credentials['email'], 'password' => $credentials['password']])) {
            // Si l'authentification est réussie, récupérer l'utilisateur connecté
            $user = User::find(Auth::user()->id);
            // Créer un token si vous utilisez Laravel Passport
            $token = $user->createToken('LaravelPassportAuth')->accessToken;
            return [
                'success' => true,
                'user' => $user,
                'token' => $token
            ];
        } else {
            // Si l'authentification échoue
            return [
                'success' => false,
                'message' => 'Informations d\'identification invalides'
            ];
        }
    }
    public function logout()
    {
        // Implémentez la déconnexion ici
        // Auth::user()->token()->revoke();
    }
}
