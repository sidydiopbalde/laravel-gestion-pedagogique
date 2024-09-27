<?php

namespace App\Services;

interface UserFirebaseServiceInterface
{
    public function createUser(array $data);

    public function updateUser(string $id, array $data);

    public function deleteUser(string $id);

    public function findUser(string $id);

    public function getAllUsers();

    public function findUserByEmail(string $email);

    public function findUserByPhone(string $telephone);

    public function createUserWithEmailAndPassword($email,$password);

    public function uploadImageToStorage($filePath, $fileName);
    public function setCustomUserClaims($uid, array $claims);
}
