<?php
namespace App\Http\Middleware;

use Closure;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\App;

class CheckUserRole
{
    public function handle($request, Closure $next, $role)
    {
        // Logique pour vérifier le rôle
        if (!$request->user() || !$request->user()->hasRole($role)) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }
    
        return $next($request);
    }

    protected function hasRole($userId, $role)
    {
        // Utiliser le conteneur pour obtenir l'instance de la base de données
        $database = App::make(Database::class);
        $user = $database->getReference('users/' . $userId)->getValue();

        if ($user && isset($user['roles'])) {
            return in_array($role, $user['roles']);
        }

        return false;
    }
}
