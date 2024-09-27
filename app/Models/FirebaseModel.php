<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use App\Jobs\SendRelanceEmail;
class FirebaseModel 
{
    protected $database;
    protected $auth;
    protected $storage;
    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount("/var/www/firebase-key.json")
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
        // dd($referentielId, $path);
        // Chemin vers le noeud des référentiels dans la Realtime Database
        $referentielsRef = $this->database->getReference($path);
        
        // Récupérer toutes les données de référentiels
        $referentiels = $referentielsRef->getValue();
        // dd($referentiels);
        // Parcourir les référentiels pour trouver celui correspondant à l'ID
        foreach ($referentiels as $key => $referentiel) {
            if ($key === $referentielId) {
                return $referentiel; // Retourner le référentiel correspondant
            }
        }
        
        // Si le référentiel n'est pas trouvé, retourner null ou une exception
        return null; // ou throw new NotFoundException("Référentiel non trouvé");
    }
    public function findRefById($referentielId)
    {
        // dd($referentielId, $path);
        // Chemin vers le noeud des référentiels dans la Realtime Database
        $referentielsRef = $this->database->getReference("referentiels");
        
        // Récupérer toutes les données de référentiels
        $referentiels = $referentielsRef->getValue();
        // dd($referentiels);
        // Parcourir les référentiels pour trouver celui correspondant à l'ID
        foreach ($referentiels as $key => $referentiel) {
            if ($key === $referentielId) {
                return $referentiel; // Retourner le référentiel correspondant
            }
        }
        
        // Si le référentiel n'est pas trouvé, retourner null ou une exception
        return null; // ou throw new NotFoundException("Référentiel non trouvé");
    }
    public function findInactifs()
    {
        try {
            // Récupérer la promotion active
            $promotionActive = $this->getActivePromotion();
            
            // Vérifier si la promotion active existe
            if (!$promotionActive) {
                return response()->json(['message' => 'Aucune promotion active trouvée'], 404);
            }
    
            // Initialiser un tableau pour stocker les apprenants inactifs
            $apprenantsInactifs = [];
    
            // Parcourir les référentiels de la promotion active
            foreach ($promotionActive as $referentiel) {
                foreach ($referentiel['referentiels'] as $apprenant) {
                // dd($ref);
                if (isset($apprenant['apprenants'])) {
                    foreach ($apprenant['apprenants'] as $apprenant) {
                        // Vérifier si le statut de l'apprenant est "inactive"
                        if (isset($apprenant['statut']) && $apprenant['statut'] === 'Inactif') {
                            $apprenantsInactifs[] = $apprenant; // Ajouter l'apprenant à la liste des inactifs
                        }
                    }
                }
            }}
    
            // Vérifier s'il y a des apprenants inactifs
            if (empty($apprenantsInactifs)) {
                return response()->json(['message' => 'Aucun apprenant inactif trouvé'], 200);
            }
    
            // Préparer la réponse
            return response()->json(['apprenants_inactifs' => array_values($apprenantsInactifs)], 200);
    
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des apprenants inactifs : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }
    
    
    // public function relancerApprenant($id)
    // {
    //     try {
    //         // Récupérer l'apprenant par son ID dans Firebase
    //         // $apprenantRef = $this->database->getReference('apprenants')->document($id);
    //         $apprenant = $apprenantRef->snapshot();

    //         if (!$apprenant->exists()) {
    //             return response()->json(['message' => 'Apprenant non trouvé'], 404);
    //         }

    //         $data = $apprenant->data();

    //         // Vérifier si le compte est déjà activé
    //         if ($data['statut'] === 'Actif') {
    //             return response()->json(['message' => 'Le compte de l\'apprenant est déjà activé'], 400);
    //         }

    //         // Envoi du mail de relance via un job
    //         SendRelanceEmail::dispatch($data['email'], $data['nom'], $data['password']);

    //         return response()->json(['message' => 'Relance envoyée avec succès'], 200);

    //     } catch (Exception $e) {
    //         return response()->json(['message' => 'Erreur lors de la relance de l\'apprenant', 'error' => $e->getMessage()], 500);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Erreur serveur', 'error' => $e->getMessage()], 500);
    //     }
    // }
    public function setCustomUserClaims($uid, array $claims)
    {
        $this->auth->setCustomUserClaims($uid, $claims);
    }
    
    public function filterByReferentielsAndStatus(array $filters)
    {
        try {
            $apprenants = $this->getAll("apprenants");
            $promotionEnCoursId = $this->getPromotionEnCoursId(); 
    
            if (!$promotionEnCoursId) {
                return response()->json(['error' => 'Aucune promotion active trouvée'], 404);
            }
    
            // Appliquer le filtre pour la promotion en cours par défaut si aucun filtre n'est fourni
            // if (empty($filters['referentiel'])) {
            //     $filters['referentiel'] = $promotionEnCoursId;
            // }
    
            // Filtrer les apprenants par le référentiel
            // if (!empty($filters['referentiel'])) {
            //     $apprenants = array_filter($apprenants, function ($apprenant) use ($filters) {
            //         return isset($apprenant['referentiels']['id']) && $apprenant['referentiels']['id'] == (int)$filters['referentiel'];
            //     });
            // }
           
            // Filtrer les apprenants par statut utilisateur
            if (!empty($filters['statut'])) {
                $apprenants = array_filter($apprenants, function ($apprenant) use ($filters) {
                    return isset($apprenant['user']['statut']) && $apprenant['user']['statut'] == $filters['statut'];
                });
            }
            return array_values($apprenants); 
    
        } catch (\Exception $e) {
            Log::error('Erreur lors du filtrage des apprenants dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function findApprenantById($id, array $filters)
    {
        try {
            // Récupérer les données de l'apprenant dans Firebase
            $apprenant = $this->findNoeudById($id, "apprenants");
            // dd($apprenant);
            if (!$apprenant) {
                return response()->json(['message' => 'Apprenant non trouvé'], 404);
            }
    
            $referentiel = isset($apprenant['referentiels']) ? $apprenant['referentiels'] : null;
    
           
            $presences = isset($apprenant['presences']) ? $apprenant['presences'] : [];
            // dd($presences);
            if (isset($filters['presence'])) {
                $moisFiltre = $filters['presence'];
                // Filtrer les présences en fonction du mois fourni
                $presences = array_filter($presences, function ($mois, $data) use ($moisFiltre) {
                    return $mois === $moisFiltre;
                }, ARRAY_FILTER_USE_BOTH);
            }
    
            // Filtrage des notes par module
            // $notes = isset($apprenant['notes']) ? $apprenant['notes'] : [];
            $competences = $apprenant['referentiels']['competences'] ?? [];
            $notes = [];
            
            foreach ($competences as $competence) {
                foreach ($competence['modules'] as $module) {
                    // Vérifier si le module contient des notes
                    if (isset($module['note'])) {
                        $notes[] = [
                            'module_nom' => $module['description'],
                            'note1' => $module['note']['note1'] ?? null,
                            'note2' => $module['note']['note2'] ?? null
                        ];
                    }
                }
            }
            
            if (isset($filters['notes'])) {
                $moduleFiltre = $filters['notes'];
                // Filtrer les notes en fonction du module fourni
                $notes = array_filter($notes, function ($note) use ($moduleFiltre) {
                    return isset($note['description']) && $note['description'] == $moduleFiltre;
                });
            }
            
            $response = [
                'apprenant' => [
                    'id' => $id,
                    'nom' => $apprenant['user']['nom'],
                    'prenom' => $apprenant['user']['prenom'],
                    'email' => $apprenant['user']['email'],
                    'statut' => $apprenant['user']['statut'],
                    'photo' => $apprenant['user']['photoCouverture'],
                    'referentiel' => $referentiel,
                    'presences' => array_values($presences), 
                    'notes' => array_values($notes) 
                ]
            ];
    
            return response()->json($response, 200);
    
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des informations de l\'apprenant : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }
       
    private function getPromotionEnCoursId()
    {
        // Récupérer les promotions avec l'état "Actif"
        $promotionEnCours = $this->database->getReference('promotions')
            ->orderByChild('etat')
            ->equalTo('Actif')
            ->getValue();

        if (!empty($promotionEnCours)) {
            // Retourner l'ID de la première promotion active trouvée
            return key($promotionEnCours);
        }

        // Retourner null si aucune promotion active n'est trouvée
        return null;
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
            $noeuds = $this->database->getReference($path);
            $noeuds = $noeuds->getValue(); 

            if ($noeuds) {
                return $noeuds; 
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
        Log::info('Création d\'un utilisateur Firebase avec email : ' . $email);
        try {
            $user = $this->auth->createUser([
                'email' => $email,
                'password' => $password,
            ]);
            return $user->uid;
            // Log::info('Utilisateur Firebase créé avec UID : ' . $user->uid);
    
        } catch (Exception $e) {
            Log::error('Firebase Auth Error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l\'utilisateur Firebase.'], 500);
        } catch (Exception $e) {
            Log::error('Firebase Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur avec Firebase.'], 500);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'utilisateur Firebase : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l\'utilisateur dans Firebase.'], 500);
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