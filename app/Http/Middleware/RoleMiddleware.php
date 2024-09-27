<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est authentifié
        if (!$user) {
            return response()->json(['error' => 'Non autorisé.'], 403);
        }

        // Vérifier le rôle de l'utilisateur
        if ($user->role !== $role) {
            return response()->json(['error' => 'Accès interdit.'], 403);
        }

        return $next($request);
    }
}
    
