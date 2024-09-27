<?php

namespace App\Services;

use App\Repository\UserFirebaseRepository;

class UserFirebaseService implements UserFirebaseServiceInterface
{
    protected $userRepository;

    public function __construct(UserFirebaseRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function setCustomUserClaims($uid, array $claims)
    {
        return $this->userRepository->setCustomUserClaims($uid, $claims);
    }
    public function updateUser(string $id, array $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(string $id)
    {
        return $this->userRepository->delete($id);
    }

    public function findUser(string $id)
    {
        return $this->userRepository->find($id);
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }

    public function findUserByEmail(string $email)
    {
        return $this->userRepository->findUserByEmail($email); 
    }
    public function findUserByPhone(string $telephone)
    {
        return $this->userRepository->findUserByPhone($telephone); 
    }
    public function createUserWithEmailAndPassword($email,$password)
    {
        // dd($this->userRepository->createUserWithEmailAndPassword($email,$password));
        return $this->userRepository->createUserWithEmailAndPassword($email,$password); 
    }
    public function uploadImageToStorage($filePath, $fileName)
    {
        return $this->userRepository->uploadImageToStorage($filePath, $fileName); 
    }
}
