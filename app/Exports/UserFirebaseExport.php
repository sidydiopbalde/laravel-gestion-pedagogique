<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserFirebaseExport implements FromCollection, WithHeadings
{
    protected $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    /**
     * Retourne les utilisateurs sous forme de collection.
     */
    public function collection()
    {
        // Assurez-vous que les données sont au bon format
        return $this->users->map(function ($user) {
            return [
                'name' => $user['name'],
                'email' => $user['email'],
                'fonction_id' => $user['fonction_id'],
                // Ajouter d'autres champs ici si nécessaire
            ];
        });
    }

    /**
     * Définir les en-têtes des colonnes Excel.
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Fonction ID',
        ];
    }
}

