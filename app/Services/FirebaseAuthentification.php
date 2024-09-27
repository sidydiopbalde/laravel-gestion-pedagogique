<?php


namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseAuthentification implements AuthentificationServiceInterface
{
    protected $firebaseApiKey;

    public function __construct()
    {
        $this->firebaseApiKey = env('FIREBASE_API_KEY'); // Clé API Firebase
    }

    public function authenticate($credentials)
{
    try {
        // Créer un client HTTP
        $client = new Client();
        // Assurez-vous que $credentials est bien un tableau
        if (is_string($credentials)) {
            // Si c'est une chaîne, on suppose que c'est directement le token
            $email = $password = "null";
        } elseif (is_array($credentials)) {
            // Si c'est un tableau, on récupère l'email et le mot de passe
            $email = $credentials['email'] ?? null;
            $password = $credentials['password'] ?? null;
        } else {
            throw new Exception("Format de credentials non valide");
        }

        // Vérifier si l'email et le mot de passe sont présents
        if (!$email || !$password) {
            throw new Exception("Email ou mot de passe non trouvé dans les credentials");
        }

        // Faire une requête POST à l'API Firebase Authentication
        $response = $client->post('https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=' . $this->firebaseApiKey, [
            'json' => [
                'email' => $email,
                'password' => $password,
                'returnSecureToken' => true,
            ],
        ]);

        // Décoder la réponse JSON
        $body = json_decode((string) $response->getBody(), true);

        // Vérifier si la requête a réussi
        if (!isset($body['idToken'])) {
            throw new Exception('Échec de l\'authentification. Jeton non trouvé.');
        }

        // Récupérer le jeton et d'autres informations utilisateur
        $idToken = $body['idToken'];
        $refreshToken = $body['refreshToken'];

        return [
            'success' => true,
            'token' => $idToken,
            'refresh_token' => $refreshToken,
        ];
    } catch (Exception $e) {
        Log::error('Erreur d\'authentification Firebase: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Échec de l\'authentification via Firebase: ' . $e->getMessage(),
        ];
    }
}


    public function register($credentials)
    {
        // Vous pouvez créer un utilisateur avec l'API REST si nécessaire
        // Pour cet exemple, nous traitons l'enregistrement de manière similaire à l'authentification
        return $this->authenticate($credentials);
    }

    public function refreshToken($request)
    {
        // Vous pouvez gérer le rafraîchissement des jetons si nécessaire
        // Pour cet exemple, nous ne gérons pas le rafraîchissement ici
        return [
            'success' => false,
            'message' => 'Le rafraîchissement des jetons n\'est pas pris en charge dans cet exemple.',
        ];
    }

    public function logout()
    {
        // La gestion de la déconnexion côté serveur n'est pas nécessaire pour Firebase, car le client peut se déconnecter directement
        return [
            'success' => true,
            'message' => 'Déconnexion réussie.',
        ];
    }

    public function login($credentials)
    {
        
    }
}