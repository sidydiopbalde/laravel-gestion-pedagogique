<?php

namespace App\Services;

use App\Repository\PromoFirebaseRepository;
use App\Repository\ReferentielFirebaseRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class PromotionFirebaseService implements PromotionFirebaseInterface
{
    protected $promotionrepository;

    public function __construct(PromoFirebaseRepository $promotionrepository)
    {
        $this->promotionrepository = $promotionrepository;
    }
 
    public function createPromotion(array $data)
    {
        if ($this->checkIfLibelleExists($data['libelle'])) {
            return response()->json(['error' => 'Le libellé de la promotion existe déjà.'], 400);
        }
      
        if (!isset($data['date_fin']) && isset($data['duree'])) {
            $data['date_fin'] = \Carbon\Carbon::parse($data['date_debut'])->addMonths($data['duree'])->format('Y-m-d');
        } elseif (!isset($data['duree']) && isset($data['date_fin'])) {
            $data['duree'] = \Carbon\Carbon::parse($data['date_debut'])->diffInMonths($data['date_fin']);
        }

        // Par défaut, l'état de la promotion est "Inactif" à moins qu'il soit activé
        $data['etat'] = $data['etat'] ?? 'Inactif';

        try {
            // Si la promotion est activée, désactiver toutes les autres
            if ($data['etat'] === 'Actif') {
                $this->deactivateOtherPromotions();
            }

            // Générer un ID unique pour la promotion
            // $promotionId = $this->database->getReference('promotions')->push()->getKey();
            $randomId = random_int(10, 99); 
            // dd($randomId);
            // Structure de la promotion
            $promotionData = [
                'libelle' => $data['libelle'],
                'id'=>$randomId,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'duree' => $data['duree'],
                'etat' => $data['etat'],
                'referentiels' => $this->formatReferentiels($data['referentiels'] ?? []),
                'photo' => $data['photo'] ?? null,
            ];

            // Enregistrement dans la Realtime Database
            $this->promotionrepository->create($promotionData);

            return response()->json(['message' => 'Promotion créée avec succès.'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    protected function checkIfLibelleExists(string $libelle): bool
    {
        try {
            $promotions = $this->promotionrepository->getAllPromotions();
            if ($promotions) {
                foreach ($promotions as $promotion) {
                    if (isset($promotion['libelle']) && $promotion['libelle'] === $libelle) {
                        return true;
                    }
                }
            }
            return false;
        } catch (Exception $e) {
            throw new \Exception("Erreur lors de la vérification du libellé: " . $e->getMessage());
        }
    }
    public function deactivateOtherPromotions()
    {
        // Désactiver toutes les autres promotions
        $this->promotionrepository->deactivateOtherPromotions();
    }
    protected function formatReferentiels(array $referentiels)
    {
        $formattedReferentiels = [];
        
        foreach ($referentiels as $referentielId) {
            // Récupérer les données du référentiel depuis la base de données
            $referentiel = $this->promotionrepository->findReferentielById($referentielId);
            // dd($referentiel);
            if ($referentiel) {
                $referentielData = [
                    'id' => $referentiel['id'], // Utiliser l'ID existant
                    'code' => $referentiel['code'],
                    'libelle' => $referentiel['libelle'],
                    'description' => $referentiel['description'] ?? null,
                    'photo' => $referentiel['photo'] ?? null,
                    'statut' => 'Actif', // Par défaut, le référentiel est actif
                    'competences' => $this->formatCompetences($referentiel['competences'] ?? []),
                    'apprenants' => $this->formatApprenants($referentiel['apprenants'] ?? [])
                ];
    
                $formattedReferentiels[] = $referentielData;
            } else {
                // Gérer le cas où le référentiel n'est pas trouvé, si nécessaire
                // Par exemple, vous pouvez loguer un message d'erreur ou lever une exception
                Log::warning("Référentiel non trouvé pour l'ID: $referentielId");
            }
        }
    
        return $formattedReferentiels;
    }
    


    protected function formatCompetences(array $competences)
    {
        $formattedCompetences = [];
        foreach ($competences as $competence) {
            $competenceData = [
                'nom' => $competence['nom'],
                'description' => $competence['description'] ?? null,
                'duree_acquisition' => $competence['duree_acquisition'] ?? null,
                'type' => $competence['type'] ?? null,
                'modules' => $this->formatModules($competence['modules'] ?? [])
            ];

            $formattedCompetences[] = $competenceData;
        }

        return $formattedCompetences;
    }
    //get actif promotion
    public function getActivePromotion(){
        try {
            $promotion = $this->promotionrepository->getActivePromotion();
            if ($promotion) {
                // $promotion['referentiels'] = $this->formatReferentiels($promotion['referentiels']);
                return response()->json($promotion, 200);
            } else {
                return response()->json(['message' => 'Aucune promotion actuelle.'], 404);
            }
            // return response()->json($promotion, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    protected function formatModules(array $modules)
    {
        $formattedModules = [];
        foreach ($modules as $module) {
            $moduleData = [
                'nom' => $module['nom'],
                'description' => $module['description'] ?? null,
                'duree_acquisition' => $module['duree_acquisition'] ?? null,
            ];

            $formattedModules[] = $moduleData;
        }

        return $formattedModules;
    }

    protected function formatApprenants(array $apprenants)
    {
        $formattedApprenants = [];
        foreach ($apprenants as $apprenant) {
            $apprenantData = [
                'nom' => $apprenant['nom'],
                'prenom' => $apprenant['prenom'],
                'adresse' => $apprenant['adresse'],
                'email' => $apprenant['email'],
                'password' => bcrypt($apprenant['password']), // Hash du mot de passe
                'telephone' => $apprenant['telephone'],
                'role' => $apprenant['role'],
                'statut' => $apprenant['statut'] ?? 'Actif', // Par défaut, statut "Actif"
                'photo' => $apprenant['photo'] ?? null, // Photo optionnelle
                'referentiel' => $apprenant['referentiel'] ?? null // Référentiel associé à l'apprenant
            ];

            $formattedApprenants[] = $apprenantData;
        }

        return $formattedApprenants;
    }
    public function getAllPromotions(){  
        return $this->promotionrepository->getAllPromotions();
    }


    public function cloturer($id)
    {
        $promotionResponse = $this->promotionrepository->find($id);
        if ($promotionResponse instanceof \Illuminate\Http\JsonResponse) {
            $promotion = $promotionResponse->getData(true); // Convertir la réponse JSON en tableau associatif
        } else {
            $promotion = $promotionResponse;
        }
    
        if (!$promotion) {
            return response()->json(['message' => 'Promotion non trouvée'], 404);
        }
    
        // Vérifier si la date de fin est échue
        $currentDate = now();
        $dateFin = Carbon::parse($promotion['date_fin']);
        if ($dateFin->greaterThan($currentDate)) {
            return response()->json(['message' => 'La date de fin de la promotion n\'est pas encore échue'], 400);
        }
        
        // Vérifier si la promotion est déjà clôturée
        if ($promotion['etat'] === 'Clôturée') {
            return response()->json(['message' => 'La promotion est déjà clôturée'], 400);
        }
        // Mettre à jour l'état de la promotion à 'Clôturée'
        $promotion['etat'] = 'Cloturée';
        $this->promotionrepository->update($id, $promotion);
        return $promotion;
        // Envoyer les relevés de notes aux apprenants via un job
        // SendReleveNotesJob::dispatch($promotion['apprenants']);
    }

    public function getReferentielsActifs($id)
    {
        $promotion = $this->promotionrepository->find($id);
        if (!$promotion) {
            return response()->json(['message' => 'Promotion non trouvée'], 404);
        }
        $referentielsActifs = array_filter($promotion['referentiels'], function ($referentiel) {
            return $referentiel['statut'] === 'Actif';
        });
        return response()->json($referentielsActifs);
    }
    public function getStatsPromos($id)
    {
        // Récupérer la promotion depuis le repository
        $promotion = $this->promotionrepository->find($id);
    
        if (!$promotion) {
            return response()->json(['message' => 'Promotion non trouvée'], 404);
        }
    
        // Statistiques globales des apprenants
        $nbreApprenantTotal = 0;
        $nbreApprenantActif = 0;
    
        // Parcourir les référentiels pour collecter les statistiques
        $referentielsActifs = array_filter($promotion['referentiels'], function ($referentiel) {
            return $referentiel['statut'] === 'Actif';
        });
    
        // Récupérer le nombre d'apprenants par référentiel
        $referentielsStats = array_map(function ($referentiel) use (&$nbreApprenantTotal, &$nbreApprenantActif) {
            $apprenants = $referentiel['apprenants'] ?? [];
            $nbreApprenantReferentiel = count($apprenants);
    
            // Ajouter au total général
            $nbreApprenantTotal += $nbreApprenantReferentiel;
    
            // Compter le nombre d'apprenants actifs dans le référentiel
            $apprenantActifReferentiel = count(array_filter($apprenants, function ($apprenant) {
                return $apprenant['statut'] === 'Actif';
            }));
    
            // Ajouter au total général des apprenants actifs
            $nbreApprenantActif += $apprenantActifReferentiel;
    
            return [
                'id' => $referentiel['id'],
                'libelle' => $referentiel['libelle'],
                'nbre_apprenant' => $nbreApprenantReferentiel,
            ];
        }, $referentielsActifs);
    
        // Calculer le nombre d'apprenants inactifs
        $nbreApprenantInactif = $nbreApprenantTotal - $nbreApprenantActif;
    
        // Retourner les informations de la promotion avec les statistiques
        return response()->json([
            'promotion' => $promotion['libelle'],
            'nbre_apprenant_total' => $nbreApprenantTotal,
            'nbre_apprenant_actif' => $nbreApprenantActif,
            'nbre_apprenant_inactif' => $nbreApprenantInactif,
            'referentiels_actifs' => $referentielsStats,
        ]);
    }

    public function updateEtat($newEtat, $id)
    {
        // Vérifier le rôle de l'utilisateur (ex. middleware ou condition manuelle)
        // if (!auth()->user()->hasRole('Manager')) {
        //     return response()->json(['message' => 'Accès interdit'], 403);
        // }
    
        // Récupérer la promotion depuis le repository
        $promotion = $this->promotionrepository->find($id);
    
        if (!$promotion) {
            return response()->json(['message' => 'Promotion non trouvée'], 404);
        }
    
        // Si l'état est 'Actif', désactiver les autres promotions en cours
        if ($newEtat === 'Actif') {
            $promotionEncours = $this->promotionrepository->getActivePromotion();
            if ($promotionEncours && $promotionEncours['id'] != $id) {
                $promotionEncours['etat'] = 'Inactif';
                $this->promotionrepository->update($promotionEncours['id'], $promotionEncours);
            }
        }
    
        // Mettre à jour l'état de la promotion actuelle
        $promotion['etat'] = $newEtat;
    
        // Vérifiez que vous utilisez la bonne méthode de mise à jour dans le repository
        $this->promotionrepository->update($id, ['etat' => $newEtat]);
    
        return response()->json(['message' => 'Statut de la promotion mis à jour avec succès.']);
    }
    

    























    public function addCompetenceToReferentiel(string $referentielId, array $competenceData)
    {
        $referentiel = $this->promotionrepository->find($referentielId);
        
        if (!$referentiel) {
            throw new Exception("Référentiel non trouvé.");
        }
   
        $competence = $this->formatCompetences($competenceData);
        $referentiel['competences'][] = $competence;
   
        return $this->promotionrepository->update($referentielId, ['competences' => $referentiel['competences']]);
    }
   
    // Suppression d'une compétence (soft delete)
    public function removeCompetenceFromReferentiel(string $referentielId, string $competenceNom)
    {
        $referentiel = $this->promotionrepository->find($referentielId);
   
        if (!$referentiel) {
            throw new Exception("Référentiel non trouvé.");
        }
   
        // Marquer la compétence comme archivée au lieu de la supprimer définitivement
        foreach ($referentiel['competences'] as &$competence) {
            if ($competence['nom'] === $competenceNom) {
                $competence['deleted_at'] = now(); // Soft delete
                break;
            }
        }
   
        return $this->promotionrepository->update($referentielId, ['competences' => $referentiel['competences']]);
    }
   
    // Ajout de modules à une compétence existante
       public function addModuleToCompetence(string $referentielId, string $competenceNom, array $moduleData)
       {
           $referentiel = $this->promotionrepository->find($referentielId);
   
           if (!$referentiel) {
               throw new Exception("Référentiel non trouvé.");
           }
   
           foreach ($referentiel['competences'] as &$competence) {
               if ($competence['nom'] === $competenceNom) {
                   $competence['modules'][] = $this->formatModules($moduleData);
                   break;
               }
           }
   
           return $this->promotionrepository->update($referentielId, ['competences' => $referentiel['competences']]);
       }
   
       // Suppression d'un module d'une compétence
       public function removeModuleFromCompetence(string $referentielId, string $competenceNom, string $moduleNom)
       {
           $referentiel = $this->promotionrepository->find($referentielId);
   
           if (!$referentiel) {
               throw new Exception("Référentiel non trouvé.");
           }
   
           foreach ($referentiel['competences'] as &$competence) {
               if ($competence['nom'] === $competenceNom) {
                   foreach ($competence['modules'] as &$module) {
                       if ($module['nom'] === $moduleNom) {
                           $module['deleted_at'] = now(); // Soft delete du module
                           break;
                       }
                   }
               }
           }
   
           return $this->promotionrepository->update($referentielId, ['competences' => $referentiel['competences']]);
       }
    public function getCompetencesByReferentiel(string $referentielId)
    {
        return $this->promotionrepository->getCompetencesByReferentiel($referentielId);
    }
    public function getModulesByReferentiel(string $referentielId)
    {
        return $this->promotionrepository->getModulesByReferentiel($referentielId);
    }
    public function updateReferentiel(string $id, array $data)
    {
        return $this->promotionrepository->update($id, $data);
    }

    public function deleteReferentiel(string $id)
    {
        return $this->promotionrepository->delete($id);
    }

    public function findReferentiel(string $id)
    {
        return $this->promotionrepository->find($id);
    }

    public function getAllActiveReferentiels($statut)
    {
        return $this->promotionrepository->getAllStatut($statut);
    }

    public function archiveReferentiel(string $id)
    {
        // Vérification si le référentiel est utilisé dans une promotion en cours
        // Logique métier pour empêcher l'archivage

        return $this->promotionrepository->softDelete($id);
    }

    public function getArchivedReferentiels()
    {
        return $this->promotionrepository->getArchived();
    }
    public function uploadImageToStorage($filePath, $fileName)
    {
        return $this->promotionrepository->uploadImageToStorage($filePath, $fileName); 
    }

    // public function getLastReferentiel()
    // {
    //     return $this->promotionrepository->getLastReferentiel();
    // }
}
