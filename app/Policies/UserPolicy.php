<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Vérifier si l'utilisateur a le rôle 'admin'
    public function isAdmin(User $user)
    {
        return $user->role === 'admin';
    }

    // Vérifier si l'utilisateur peut gérer les utilisateurs
    public function manageUsers(User $user)
    {
        return $user->role === 'admin';
    }
}