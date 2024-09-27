<?php

namespace App\Services;

interface PromotionFirebaseInterface 
{
    public function createPromotion(array $data);
    public function getAllPromotions();
    public function deactivateOtherPromotions();  
    public function cloturer($id);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
    public function getReferentielsActifs($id);
    public function getStatsPromos($id);

    public function updateReferentiel(string $id, array $data);
    public function deleteReferentiel(string $id);
    public function findReferentiel(string $id);
    public function getAllActiveReferentiels($statut);
    public function archiveReferentiel(string $id);
    public function getArchivedReferentiels();
    public function uploadImageToStorage($filePath, $fileName);
    public function getCompetencesByReferentiel(string $referentielId);
    public function getModulesByReferentiel(string $referentielId);
    // public function getLastReferentiel();

}
