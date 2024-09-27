<?php
namespace App\Repository;
use App\Facades\ApprenantsFirebaseFacade; 

class ApprenantsFirebaseRepository implements ApprenantsFirebaseRepositoryInterface
{
    protected $firebasePath = 'apprenants'; 

    public function create(array $data)
    {
        return ApprenantsFirebaseFacade::create($this->firebasePath, $data);
    }

    public function update(string $id, array $data)
    {
        return ApprenantsFirebaseFacade::update($this->firebasePath, $id, $data);
    }
    public function delete(string $id)
    {
        return ApprenantsFirebaseFacade::delete($this->firebasePath, $id);
    }
    
    public function find(string $id)
    {
        return ApprenantsFirebaseFacade::find($this->firebasePath, $id);
    }
    public function findApprenantById( $id)
    {
        return ApprenantsFirebaseFacade::findNoeudById($id,"users");
    }
    public function findApprenantBy_ID($id,array $filters)
    {
        return ApprenantsFirebaseFacade::findApprenantById($id,$filters);
    }
    public function findInactifs()
    {
        return ApprenantsFirebaseFacade::findInactifs($this->firebasePath);
    }
    public function filterByReferentielsAndStatus(array $filters)
    {
        return ApprenantsFirebaseFacade::filterByReferentielsAndStatus($filters);
    }
    public function getAll()
    {
        return ApprenantsFirebaseFacade::getAll($this->firebasePath); 
    }

    public function findUserByEmail(string $email)
    {
        return ApprenantsFirebaseFacade::findUserByEmail($email); 
    }

    public function findUserByPhone(string $telephone)
    {
        return ApprenantsFirebaseFacade::findUserByPhone($telephone); 
    }

    public function createUserWithEmailAndPassword($email,$password)
    {
        return ApprenantsFirebaseFacade::createUserWithEmailAndPassword($email,$password); 
    }
    public function uploadImageToStorage($filePath, $fileName)
    {
        return ApprenantsFirebaseFacade::uploadImageToStorage($filePath, $fileName); 
    }
}