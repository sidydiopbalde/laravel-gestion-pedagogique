<?php

namespace App\Repository;

use App\Models\FirebaseModel;

class FirebaseRepositoryImpl implements FirebaseRepositoryInterface
{
    protected $firebaseModel;

    public function __construct(FirebaseModel $firebaseModel)
    {
        $this->firebaseModel = $firebaseModel;
    }

    public function create(array $data)
    {
        // return $this->firebaseModel->create($data);
    }

    public function update(string $id, array $data)
    {
        // return $this->firebaseModel->updateModel($id, $data);
    }

    public function delete(string $id)
    {
        // return $this->firebaseModel->deleteModel($id);
    }

    public function find(string $id)
    {
        // return $this->firebaseModel->findModel($id);
    }

    public function getAll()
    {
        // return $this->firebaseModel->getAllModels();
    }
}
