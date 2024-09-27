<?php

namespace App\Services;

interface ApprenantsFirebaseServiceInterface
{
    public function createApprenant(array $data);

    public function updateApprenants(string $id, array $data);

    public function deleteApprenants(string $id);

    public function findApprenants(string $id);

    public function findApprenantsById($id);

    public function getAllApprenants();

    public function findApprenantsByEmail(string $email);

    public function findUserByPhone(string $telephone);

    public function createUserWithEmailAndPassword($email,$password);

    public function uploadImageToStorage($filePath, $fileName);
}
