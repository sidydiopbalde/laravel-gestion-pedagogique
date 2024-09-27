<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;

class SwitchAuthMiddleware
{
    protected $firebaseAuth;
    protected $tokenRepository;

    public function __construct(FirebaseAuth $firebaseAuth, TokenRepository $tokenRepository)
    {
        $this->firebaseAuth = $firebaseAuth;
        $this->tokenRepository = $tokenRepository;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 401);
        }

        // Tentative de vérifier si c'est un token Firebase
        try {
            $verifiedToken = $this->firebaseAuth->verifyIdToken($token);
            $firebaseUser = $verifiedToken->claims()->get('sub');  // ID utilisateur Firebase

            // Authentifier l'utilisateur Firebase localement
            $localUser = User::where('firebase_uid', $firebaseUser)->first();
            if ($localUser) {
                Auth::login($localUser);
                return $next($request);
            }

            return response()->json(['error' => 'Utilisateur non trouvé dans la base locale.'], 404);

        } catch (\Exception $e) {
            // Ce n'est pas un token Firebase, vérifier avec Passport
            return $this->handlePassportAuthentication($request, $next);
        }
    }

    protected function handlePassportAuthentication($request, Closure $next)
    {
        $token = $request->bearerToken();
    
        // Vérifier le token avec Laravel Passport
        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 401);
        }
    
        $passportToken = $this->tokenRepository->find($token);
    
        if (!$passportToken) {
            return response()->json(['error' => 'Token non trouvé'], 401);
        }
    
        if ($passportToken->revoked) {
            return response()->json(['error' => 'Token révoqué'], 401);
        }
    
        $user = $passportToken->user;
    
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 401);
        }
    
        // Authentifier l'utilisateur via Passport
        Auth::login($user);
        return $next($request);
    }
    
}

