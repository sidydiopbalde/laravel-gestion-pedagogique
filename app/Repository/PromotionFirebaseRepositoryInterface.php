<?php

namespace App\Repository;

interface PromotionFirebaseRepositoryInterface
{
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function find(string $id);
    public function getAllPromotions();
    public function deactivateOtherPromotions();
}