<?php
namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use App\Services\AuthentificationServiceInterface;
use Exception;

class AuthentificationFirebase implements AuthentificationServiceInterface
{
    protected $firebaseAuth;

    public function __construct($firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth; // Injecter l'instance FirebaseAuth
    }

    public function authenticate(array $data)
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation échouée', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }
    
        try {
            // Authentification via Firebase avec email et mot de passe
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword(
                $data['email'],
                $data['password']
            );
    
            // Récupérer l'UID de l'utilisateur Firebase
            $uid = $signInResult->firebaseUserId();
            Log::info('Utilisateur connecté, Firebase UID: ' . $uid);
    
            // Générer un token personnalisé pour cet utilisateur
            $customToken = $this->firebaseAuth->createCustomToken($uid);
    
            return response()->json([
                'message' => 'Authenticated successfully',
                'firebase_token' => $customToken->toString()
            ]);
        } catch (UserNotFound $e) {
            Log::error('Utilisateur non trouvé pour l\'email: ' . $data['email']);
            return response()->json(['error' => 'User not found'], 404);
        } catch (InvalidPassword $e) {
            Log::error('Mot de passe invalide pour l\'email: ' . $data['email']);
            return response()->json(['error' => 'Invalid password'], 401);
        } catch (\Exception $e) { // Assurez-vous d'avoir une capture générique des exceptions.
            Log::error('Erreur lors de l\'authentification: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function logout()
    {
        // Implémentation de la déconnexion (si nécessaire)
        // Auth::user()->token()->revoke(); // Exemple de révocation de token
    }
}

