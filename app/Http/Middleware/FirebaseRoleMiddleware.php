<?php

namespace App\Http\Middleware;

use Closure;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Auth;

class FirebaseRoleMiddleware
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function handle($request, Closure $next, $role)
    {
        // Extraire le token Firebase du header Authorization
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 401);
        }

        // Vérifier le token Firebase et obtenir les revendications
        try {
            $verifiedToken = $this->firebaseAuth->verifyIdToken($token);
            $claims = $verifiedToken->claims()->get('role');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token non valide ou expiré'], 401);
        }

        // Vérifier le rôle dans les revendications
        if ($claims !== $role) {
            return response()->json(['error' => 'Accès interdit pour ce rôle'], 403);
        }

        return $next($request);
    }
}

