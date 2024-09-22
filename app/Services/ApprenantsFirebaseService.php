<?php

namespace App\Services;

use App\Repository\ApprenantsFirebaseRepository;

class ApprenantsFirebaseService implements ApprenantsFirebaseServiceInterface
{
    protected $apprenantsRepository;

    public function __construct(ApprenantsFirebaseRepository $apprenantsRepository)
    {
        $this->apprenantsRepository = $apprenantsRepository;
    }

    public function createApprenant(array $data)
    {
        return $this->apprenantsRepository->create($data);
    }

    public function updateApprenants(string $id, array $data)
    {
        return $this->apprenantsRepository->update($id, $data);
    }

    public function deleteApprenants(string $id)
    {
        return $this->apprenantsRepository->delete($id);
    }

    public function findApprenants(string $id)
    {
        return $this->apprenantsRepository->find($id);
    }

    public function getAllApprenants()
    {
        return $this->apprenantsRepository->getAll();
    }

    public function findApprenantsByEmail(string $email)
    {
        return $this->apprenantsRepository->findUserByEmail($email); 
    }
    public function findUserByPhone(string $telephone)
    {
        return $this->apprenantsRepository->findUserByPhone($telephone); 
    }
    public function createUserWithEmailAndPassword($email,$password)
    {
        return $this->apprenantsRepository->createUserWithEmailAndPassword($email,$password); 
    }
    public function uploadImageToStorage($filePath, $fileName)
    {
        return $this->apprenantsRepository->uploadImageToStorage($filePath, $fileName); 
    }
}
