<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class PromotionPolicy
{
    public function access(User $user): Response
    {
        // Autorise l'accès si le rôle de l'utilisateur est 'Admine ou Mananger'
        return $user->fonction && in_array($user->fonction->libelle, ['Admin', 'Manager'])
        ? Response::allow()
        : Response::deny('Vous devez être un Admin ou un Manager pour accéder à cette ressource.');
    }

 
}