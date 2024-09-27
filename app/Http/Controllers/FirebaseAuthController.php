<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthentificationServiceInterface;
use App\Services\FirebaseServiceInterface; 
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Illuminate\Support\Facades\Validator;

class FirebaseAuthController extends Controller implements AuthentificationServiceInterface
{
    protected $firebaseAuth;
    protected $firebaseService;

    public function __construct(FirebaseServiceInterface $firebaseService)
    {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $this->firebaseAuth = $factory->createAuth();
        $this->firebaseService = $firebaseService;
    }

    public function authenticate(array $credentials)
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        try {
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($email, $password);
            $uid = $signInResult->firebaseUserId();
            $customToken = $this->firebaseAuth->createCustomToken($uid);

            return [
                'message' => 'Authenticated successfully',
                'firebase_token' => $customToken->toString()
            ];
        } catch (UserNotFound $e) {
            Log::error('Utilisateur non trouvé: ' . $email);
            return ['error' => 'User not found'];
        } catch (InvalidPassword $e) {
            Log::error('Mot de passe invalide: ' . $email);
            return ['error' => 'Invalid password'];
        } catch (Exception $e) {
            Log::error('Erreur d\'authentification: ' . $e->getMessage());
            return ['error' => 'Invalid credentials'];
        }
    }

    public function logout()
    {
        // Logique de déconnexion si nécessaire
    }

    // Autres méthodes (authenticateGoogle, etc.) si nécessaire...
}

// namespace App\Http\Controllers;

// use App\Models\User;
// use App\Services\FirebaseServiceInterface; // Importez l'interface de service
// use Exception;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Routing\Controller;
// use Illuminate\Http\Request;
// use Kreait\Firebase\Factory;
// use Kreait\Firebase\Auth as FirebaseAuth;
// use Kreait\Firebase\Exception\Auth\InvalidPassword;
// use Kreait\Firebase\Exception\Auth\UserNotFound;
// use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
// use Illuminate\Support\Facades\Validator;

// class FirebaseAuthController extends Controller
// {
//     protected $firebaseAuth;
//     protected $firebaseService; // Ajout de la propriété pour le service Firebase

//     public function __construct(FirebaseServiceInterface $firebaseService)
//     {
//         $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
//         $this->firebaseAuth = $factory->createAuth();
//         $this->firebaseService = $firebaseService; // Injection du service Firebase
//     }

//     // Méthode pour authentifier via Google
//     public function authenticate(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'email' => 'required|email',
//             'password' => 'required|string|min:6',
//         ]);
        
//         if ($validator->fails()) {
//             Log::error('Validation échouée', $validator->errors()->toArray());
//             return response()->json($validator->errors(), 422);
//         }
    
//         try {
//             Log::info('Tentative de connexion avec l\'email: ' . $request->input('email'));
            
//             $signInResult = $this->firebaseAuth->signInWithEmailAndPassword(
//                 $request->input('email'),
//                 $request->input('password')
//             );
            
//             $uid = $signInResult->firebaseUserId();
//             Log::info('Utilisateur connecté, Firebase UID: ' . $uid);
            
//             $customToken = $this->firebaseAuth->createCustomToken($uid);
            
//             return response()->json([
//                 'message' => 'Authenticated successfully',
//                 'firebase_token' => $customToken->toString()
//             ]);
//         } catch (UserNotFound $e) {
//             Log::error('Utilisateur non trouvé pour l\'email: ' . $request->input('email'));
//             return response()->json(['error' => 'User not found'], 404);
//         } catch (InvalidPassword $e) {
//             Log::error('Mot de passe invalide pour l\'email: ' . $request->input('email'));
//             return response()->json(['error' => 'Invalid password'], 401);
//         } catch (Exception $e) {
//             Log::error('Erreur lors de l\'authentification: ' . $e->getMessage());
//             return response()->json(['error' => 'Invalid credentials'], 401);
//         }
//     }
    
//     public function authenticateGoogle(Request $request)
//     {
//         $token = $request->input('google_id_token');
//         try {
//             // Vérification du token Google avec Firebase
//             $verifiedIdToken = $this->firebaseAuth->verifyIdToken($token);
//             $uid = $verifiedIdToken->claims()->get('sub');

//             // Créer ou mettre à jour l'utilisateur dans votre base de données
//             // $user = User::updateOrCreate([...]);

//             return response()->json(['token' => $token]);

//         } catch (Exception $e) {
//             Log::error('Erreur lors de la vérification du token Google : ' . $e->getMessage());
//             return response()->json(['error' => 'Invalid token'], 401);
//         }
//     }

//     // Authentification avec un token de fournisseur (Google, GitHub, Facebook)
//     public function authenticateWithProviderToken(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'provider_token' => 'required|string',
//             'provider' => 'required|string|in:google,github,facebook',
//         ]);

//         if ($validator->fails()) {
//             return response()->json($validator->errors(), 422);
//         }

//         try {
//             // Vérification du token via Firebase
//             $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->input('provider_token'));
//             $uid = $verifiedIdToken->claims()->get('sub');

//             // Récupérer l'utilisateur via le UID Firebase
//             $user = $this->firebaseAuth->getUser($uid);

//             // Générer un JWT Laravel
//             $customToken = $this->firebaseAuth->createCustomToken($uid);

//             return response()->json([
//                 'message' => 'Authenticated successfully',
//                 'firebase_token' => $customToken->toString()
//             ]);
//         } catch (FailedToVerifyToken $e) {
//             return response()->json(['error' => 'Invalid provider token'], 401);
//         }
//     }

//     // Authentification avec email et mot de passe
// }
