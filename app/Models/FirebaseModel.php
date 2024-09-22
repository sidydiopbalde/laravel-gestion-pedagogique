<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Facades\FirebaseFacade;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
use Kreait\Firebase\Auth;
class FirebaseModel 
{
    protected $database;
    protected $auth;
    protected $storage;
    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $factory->createDatabase();
        $this->auth = $factory->createAuth();
        $this->storage = $factory->createStorage();
     
    }

    public function getDatabase()
    {
        return $this->database;
    }

    // Méthode pour créer une nouvelle entrée dans Firebase
    public function create($path, $data)
    {
        try {
            $reference = $this->database->getReference($path);
            $key = $reference->push()->getKey();
            $reference->getChild($key)->set($data);
            return $key;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Méthode pour rechercher une entrée spécifique dans Firebase
    public function find($path, $id)
    {
        try {
            $reference = $this->database->getReference($path);
            $allReferentiels = $reference->getValue();
            foreach ($allReferentiels as $key => $referentiel) {
                if (isset($referentiel['id']) && $referentiel['id'] == (int)$id) {
                    return $referentiel;
                }
            }
            // dd($referentiel);
            return response()->json(['error' => 'noeuds non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function findNoeudById(string $referentielId, string $path)
    {
        // Chemin vers le noeud des référentiels dans la Realtime Database
        $referentielsRef = $this->database->getReference($path);
    
        // Récupérer toutes les données de référentiels
        $referentiels = $referentielsRef->getValue();
    
        // Parcourir les référentiels pour trouver celui correspondant à l'ID
        foreach ($referentiels as $key => $referentiel) {
            if ($key === $referentielId) {
                return $referentiel; // Retourner le référentiel correspondant
            }
        }
    
        // Si le référentiel n'est pas trouvé, retourner null ou une exception
        return null; // ou throw new NotFoundException("Référentiel non trouvé");
    }
    
    

    // Méthode pour mettre à jour une entrée spécifique dans Firebase
    public function update($path, $id, $data)
    {
        try {
            $reference = $this->database->getReference($path);
            $allReferentiels = $reference->getValue();
    
            // Vérifier si le référentiel existe
            $referentielFound = false;
            foreach ($allReferentiels as $key => $referentiel) {
                if (isset($referentiel['id']) && $referentiel['id'] == (int)$id) {
                    $referentielFound = true;
                    break; // Sortir de la boucle si trouvé
                }
            }
    
            if (!$referentielFound) {
                return response()->json(['error' => 'Référentiel non trouvé'], 404);
            }
    
            // Mise à jour du neoud en utilisant la bonne clé
            $reference->getChild($key)->update($data);
            return response()->json(['success' => 'Mise à jour réussie']);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Méthode pour supprimer une entrée spécifique dans Firebase
    public function delete($path, $id)
    {
        try {
            $reference = $this->database->getReference($path);
            $allReferentiels = $reference->getValue();
    
            // Vérifier si le référentiel existe
            $referentielFound = false;
            $referentielKey = null; // Pour garder la clé du référentiel
    
            foreach ($allReferentiels as $key => $referentiel) {
                if ($referentiel['id'] == (int)$id) {
                    $referentielFound = true;
                    $referentielKey = $key; // Stocker la clé pour la mise à jour
                    break;
                }
            }
    
            if (!$referentielFound) {
                return response()->json(['error' => 'Référentiel non trouvé'], 404);
            }
    
            // Mettre à jour le champ 'actif' pour effectuer un soft delete
            $reference->getChild($referentielKey)->update(['actif' => false]); // Définir le champ actif à false
    
            return response()->json(['success' => 'Référentiel archivé avec succès']);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Méthode pour tester la connexion Firebase (optionnelle)
    public function test()
    {
        Log::info('Testing Firebase connection');
        $reference = $this->database->getReference('test');
        $reference->set([
            'date' => now()->toDateTimeString(),
            'content' => 'Firebase connection test',
        ]);
        Log::info('Data pushed to Firebase');
    }

    // Exemple d'utilisation pour stocker des données via une requête
    public function store($request)
    {
        $reference = $this->database->getReference('test'); // Remplacez 'test' par votre chemin
        $newData = $reference->push($request);
        return response()->json($newData->getValue());
    }
    // Méthode pour obtenir tous les utilisateurs depuis Firebase
    public function getAll($path)
    {
        try {
            $reference = $this->database->getReference($path);
            $users = $reference->getValue(); 

            if ($users) {
                return $users; 
            } else {
                return []; 
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des utilisateurs dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function findUserByEmail(string $email)
    {
        $users = $this->getAll('users');
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
    public function findUserByPhone(string $telephone)
    {
        $users = $this->getAll('users');
        // dd($users, $telephone);
        foreach ($users as $user) {
            if (isset($userData['telephone'])) {
            if ($user['telephone'] === $telephone) {
                return $user;
            }
        }
        }
        return null;
    }
    public function createUserWithEmailAndPassword($email, $password)
    {
    // Obtenez l'instance de Firebase Auth
        try {
            $user = $this->auth->createUser(['email'=>$email, 'password'=>$password]);
            return $user->uid; // Retournez l'ID de l'utilisateur créé
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'utilisateur Firebase : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l\'utilisateur dans Firebase'], 500);
        }
    }
    public function uploadImageToStorage($filePath, $fileName)
    {
        try {
            // Récupérer le bucket de Firebase Storage
            $bucket = $this->storage->getBucket();

            // Ouvrir le fichier et le télécharger
            $file = fopen($filePath, 'r');
            $bucket->upload($file, [
                'name' => $fileName // Nom du fichier dans le bucket
            ]);

            // Obtenez l'URL de téléchargement
            $object = $bucket->object($fileName);
            $url = $object->signedUrl(new \DateTime('tomorrow')); // URL temporaire d'un jour

            Log::info('Image téléchargée avec succès sur Firebase Storage : ' . $url);
            return $url;

        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement de l\'image dans Firebase Storage : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getLastReferentiel()
    {
        // Récupérer tous les référentiels depuis Firebase
        $referentiels = $this->database->getReference('referentiels')->getValue();

        // Si aucun référentiel n'existe, renvoyer null
        if (!$referentiels) {
            return null; // Pas de référentiels existants
        }

        // Utiliser `collect` pour trier les référentiels par ID décroissant
        return collect($referentiels)->sortByDesc('id')->first();
    }
    public function deactivateOtherPromotions()
    {
        try {
            
            $promotions = $this->database->getReference('promotions')->getValue();

            if ($promotions) {
                foreach ($promotions as $key => $promotion) {
                    if (isset($promotion['etat']) && $promotion['etat'] === 'Actif') {
                        // Désactiver les autres promotions actives
                        $this->database->getReference("promotions/{$key}/etat")->set('Inactif');
                    }
                }
            }
        } catch (Exception $e) {
            throw new \Exception("Erreur lors de la désactivation des promotions: " . $e->getMessage());
        }
    }
    //get activePromotions
    public function getActivePromotion()
    {
        $promotions = $this->getAll('promotions');
        // dd($promotions);
        if ($promotions) {
            return collect($promotions)->where('etat', 'Actif')->all();
        }

        return [];
    }
}
