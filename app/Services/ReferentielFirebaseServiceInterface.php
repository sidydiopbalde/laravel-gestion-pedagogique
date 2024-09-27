<?php

namespace App\Services;

interface ReferentielFirebaseServiceInterface
{
    public function createReferentiel(array $data);
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
