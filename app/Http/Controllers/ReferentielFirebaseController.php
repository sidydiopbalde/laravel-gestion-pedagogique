<?php

namespace App\Http\Controllers;

use App\Services\ReferentielFirebaseService;
use Illuminate\Http\Request;
use Exception;

class ReferentielFirebaseController extends Controller
{
    protected $referentielService;

    public function __construct(ReferentielFirebaseService $referentielService)
    {
        $this->referentielService = $referentielService;
    }

    public function getArchivedReferentiels()
    {
        $referentiels = $this->referentielService->getArchivedReferentiels();

        return response()->json($referentiels);
    }
      // Mise à jour d'un référentiel
      public function update(Request $request, $id)
      {
          $data = $request->validate([
              'code' => 'required|string',
              'libelle' => 'required|string',
              'description' => 'nullable|string',
              'photo' => 'nullable|string',
              'competences' => 'nullable|array'
          ]);
  
          try {
              $referentiel = $this->referentielService->updateReferentiel($id, $data);
              return response()->json(['message' => 'Référentiel mis à jour avec succès.', 'data' => $referentiel], 200);
          } catch (Exception $e) {
              return response()->json(['message' => $e->getMessage()], 400);
          }
      }
    // Suppression d'un référentiel
    public function delete($id)
    {
        try {
            $this->referentielService->deleteReferentiel($id);
            return response()->json(['message' => 'Référentiel supprimé avec succès.'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function show($id)
    {
        $filtre = request()->query('filtre'); // Récupérer le paramètre 'filtre' de l'URL
        
        if ($filtre === 'competence') {
            // Récupérer les compétences et les modules pour chaque compétence
            $competences = $this->referentielService->getCompetencesByReferentiel($id);
            
            // Parcourir les compétences pour récupérer leurs modules
            $result = array_map(function($competence) {
                return [
                    'nom' => $competence['nom'],
                    'description' => $competence['description'],
                    'duree_acquisition' => $competence['duree_acquisition'],
                    'modules' => $competence['modules'] ?? []
                ];
            }, $competences);
            
            return response()->json($result, 200);
        }
        
        if ($filtre === 'module') {
            // Récupérer les modules directement associés au référentiel
            $modules = $this->referentielService->getModulesByReferentiel($id);
            return response()->json($modules, 200);
        }
        
        // Si aucun filtre n'est fourni, on retourne le référentiel complet
        // $referentiel = $this->referentielService->find($id);
        // return response()->json($referentiel, 200);
    }

    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'code' => 'required|unique:referentiels',
        //     'libelle' => 'required|unique:referentiels',
        //     'description' => 'required',
        //     'photo' => 'nullable|image',
        //     'competences' => 'nullable|array',
        //     'competences.*.nom' => 'required',
        //     'competences.*.description' => 'required',
        //     'competences.*.duree_acquisition' => 'required|integer',
        //     'competences.*.type' => 'required|in:backend,frontend',
        //     'competences.*.modules' => 'nullable|array',
        //     'competences.*.modules.*.nom' => 'required',
        //     'competences.*.modules.*.description' => 'required',
        //     'competences.*.modules.*.duree_acquisition' => 'required|integer',
        // ]);

        try {
            $data = $request->all();
            // dd($data);
            // Gestion de l'image de couverture
            // if ($request->hasFile('photo')) {
            //     $photoPath = $request->file('photo')->store('cover_photos', 'public');
            //     $data['photo'] = $photoPath;
            // }
            if ($request->hasFile('photo')) {
                // Générer un nom unique pour l'image
                $fileName = 'photos/' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
                $filePath = $request->file('photo')->getPathname();
    
                // Appeler le service pour uploader l'image sur Firebase Storage
                $photoUrl = $this->referentielService->uploadImageToStorage($filePath, $fileName);
    
                if ($photoUrl) {
                    $data['photo'] =  $filePath;
                } else {
                    return response()->json(['error' => 'Erreur lors de l\'upload de la photo.'], 500);
                }
            }
            // Appel au service pour créer le référentiel
            $referentiel = $this->referentielService->createReferentiel($data);

            return response()->json(
                ['referentiel_Uid'=>$referentiel,
                'message'=>'referentiel crée avec succés',
                'data'=>$data, 201]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    //list actifs referentiels
    public function index(Request $request)
    {
        $statut = $request->input('statut', 'Actif');
        $referentiels = $this->referentielService->getAllActiveReferentiels($statut);
        return response()->json($referentiels);
    }
    public function getReferentielById($id)
    {
        try {
            $referentiel = $this->referentielService->findReferentiel($id);
            return response()->json($referentiel, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    // Filtre par compétences d'un référentiel
    public function getCompetencesByReferentiel($id)
    {
        try {
            $competences = $this->referentielService->getCompetencesByReferentiel($id);
            return response()->json($competences, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    // Filtre par modules d'un référentiel
    public function getModulesByReferentiel($id)
    {
        try {
            $modules = $this->referentielService->getModulesByReferentiel($id);
            return response()->json($modules, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }


}
