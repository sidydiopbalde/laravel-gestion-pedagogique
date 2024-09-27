<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class ServiceFirebase implements ServiceFirebaseInterface
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
        ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
        ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));  
         $this->database = $factory->createDatabase();
    }

    public function create($path, $data)
    {
        try {
            $reference = $this->database->getReference($path);
            $key = $reference->push()->getKey();
            $reference->getChild($key)->set($data);
            return $key;
        } catch (\Exception $e) {
            // Gérer l'exception
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function find($path, $id)
    {
        $reference = $this->database->getReference($path.'/'.$id);
        return $reference->getValue();
    }

    public function update($path, $id, $data)
    {
        $reference = $this->database->getReference($path.'/'.$id);
        $reference->update($data);
    }

    public function delete($path, $id)
    {
        $reference = $this->database->getReference($path.'/'.$id);
        $reference->remove();
    }
}
