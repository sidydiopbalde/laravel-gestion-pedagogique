<?php

namespace App\Services;

interface ServiceFirebaseInterface
{
    public function create($path, $data);
    public function find($path, $id);
    public function update($path, $id, $data);
    public function delete($path, $id);
}
