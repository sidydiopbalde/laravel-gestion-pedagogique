<?php

namespace App\Repository;
use App\Facades\PromotionFirebaseFacade as Promotions;
class PromoFirebaseRepository implements PromotionFirebaseRepositoryInterface
{
    protected $firebaseNode;

    public function __construct()
    {
        $this->firebaseNode = 'promotions';
    }

    public function deactivateOtherPromotions(){
        Promotions::deactivateOtherPromotions($this->firebaseNode);
    }
    public function findReferentielById(string $referentielId)
    {
        return Promotions::findNoeudById($referentielId,"users");
        // return Promotions::findNoeudById($referentielId,$this->firebaseNode);
    }
    public function getActivePromotion()
    {
        return Promotions::getActivePromotion($this->firebaseNode);
    }
    public function getAllPromotions()
    {
        return Promotions::getAll($this->firebaseNode);
    }
    public function getAllReferentiels()
    {
        return Promotions::getAll("referentiels");
    }
    public function create(array $data)
    {
        return Promotions::create($this->firebaseNode, $data);
    }

    public function update(string $id, array $data)
    {
        return Promotions::update($this->firebaseNode,$id, $data);
    }

    public function delete(string $id)
    {
        return Promotions::delete($this->firebaseNode,$id);
    }

    public function find(string $id)
    {
        return Promotions::find($this->firebaseNode, $id);
    }

    public function getAllStatut($statut)
    {
        $allReferentiels = Promotions::getAll($this->firebaseNode);
    
        // Utilisez `use` pour capturer la variable `$statut` dans la closure
        return array_filter($allReferentiels, function($referentiel) use ($statut) {
            return $referentiel['statut'] === $statut;
        });
    }
    
   
    public function softDelete(string $id)
    {
        return $this->update($id, ['statut' => 'Archiver']);
    }

    public function getArchived()
    {
        $allReferentiels = Promotions::getAll($this->firebaseNode);
        // Filtrer les référentiels où 'actif' est false
        $ref = array_filter($allReferentiels, function($referentiel) {
            return isset($referentiel['actif']) && $referentiel['actif'] === false; // Comparer avec le booléen false
        });
        return $ref;
    }
    
    public function uploadImageToStorage($filePath, $fileName)
    {
        return Promotions::uploadImageToStorage($filePath, $fileName);
    }
    public function getCompetencesByReferentiel(string $referentielId)
    {
        $referentiel = Promotions::find($this->firebaseNode, $referentielId);
        return $referentiel['competences'] ?? [];
    }
    
    public function getModulesByReferentiel(string $referentielId)
    {
        $referentiel = Promotions::find($this->firebaseNode, $referentielId);
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
        $referentiels = Promotions::getLastReferentiel();
        return $referentiels;
    }
 


    // Méthodes spécifiques à PromoFirebase si nécessaire
}
