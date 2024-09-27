<?php

namespace App\Repository;

interface ReferentielFirebaseRepositoryInterface
{
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function find(string $id);
    public function getAllStatut($statut);
    public function softDelete(string $id);
    public function getArchived();
    public function uploadImageToStorage($filePath, $fileName);
    public function getCompetencesByReferentiel(string $referentielId);
    public function getModulesByReferentiel(string $referentielId);
    public function getLastReferentiel();
}
