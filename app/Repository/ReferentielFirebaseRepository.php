<?php

namespace App\Repository;

use App\Facades\ReferentielFirebaseFacade;

class ReferentielFirebaseRepository implements ReferentielFirebaseRepositoryInterface
{
    protected $firebaseNode;

    public function __construct()
    {
        $this->firebaseNode = 'referentiels';
    }

    public function create(array $data)
    {
        return ReferentielFirebaseFacade::create($this->firebaseNode, $data);
    }

    public function update(string $id, array $data)
    {
        return ReferentielFirebaseFacade::update($this->firebaseNode,$id, $data);
    }

    public function delete(string $id)
    {
        return ReferentielFirebaseFacade::delete($this->firebaseNode,$id);
    }

    public function find(string $id)
    {
        return ReferentielFirebaseFacade::find('referentiels', $id);
    }
    public function findRefById(string $id)
    {
        return ReferentielFirebaseFacade::findRefById($id);
    }
    public function getAllStatut($statut)
    {
        $allReferentiels = ReferentielFirebaseFacade::getAll($this->firebaseNode);
    
        // Utilisez `use` pour capturer la variable `$statut` dans la closure
        return array_filter($allReferentiels, function($referentiel) use ($statut) {
            return $referentiel['statut'] === $statut;
        });
    }
    
    // public function getAllInActive()
    // {
    //     $allReferentiels = ReferentielFirebaseFacade::getAll($this->firebaseNode);
    //     return array_filter($allReferentiels, function($referentiel) {
    //         return $referentiel['statut'] === 'Inactif';
    //     });
    // }
    // public function getAllArcheved()
    // {
    //     $allReferentiels = ReferentielFirebaseFacade::getAll($this->firebaseNode);
    //     return array_filter($allReferentiels, function($referentiel) {
    //         return $referentiel['statut'] === 'Archever';
    //     });
    // }
    public function softDelete(string $id)
    {
        return $this->update($id, ['statut' => 'Archiver']);
    }

    public function getArchived()
    {
        $allReferentiels = ReferentielFirebaseFacade::getAll($this->firebaseNode);
        // Filtrer les référentiels où 'actif' est false
        $ref = array_filter($allReferentiels, function($referentiel) {
            return isset($referentiel['actif']) && $referentiel['actif'] === false; // Comparer avec le booléen false
        });
        return $ref;
    }
    
    public function uploadImageToStorage($filePath, $fileName)
    {
        return ReferentielFirebaseFacade::uploadImageToStorage($filePath, $fileName);
    }
    public function getCompetencesByReferentiel(string $referentielId)
    {
        $referentiel = ReferentielFirebaseFacade::find($this->firebaseNode, $referentielId);
        return $referentiel['competences'] ?? [];
    }
    
    public function getModulesByReferentiel(string $referentielId)
    {
        $referentiel = ReferentielFirebaseFacade::find($this->firebaseNode, $referentielId);
        // Si les modules sont dans chaque compétence, vous pouvez adapter ceci pour les parcourir
        $modules = [];
        if (!empty($referentiel['competences'])) {
            foreach ($referentiel['competences'] as $competence) {
                $modules = array_merge($modules, $competence['modules'] ?? []);
            }
        }
        return $modules;
    }
    
    public function getLastReferentiel()
    {
        $referentiels = ReferentielFirebaseFacade::getLastReferentiel();
        return $referentiels;
    }
}