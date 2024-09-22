<?php

namespace App\Http\Controllers;

use App\Services\PromotionFirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\SendReleveNotesJob;
class PromotionFirebaseController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionFirebaseService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'libelle' => 'required|string|unique:promotions,libelle',
        //     'date_debut' => 'required|date',
        //     'duree' => 'nullable|integer',
        //     'date_fin' => 'nullable|date|after:date_debut',
        //     'etat' => 'nullable|in:Actif,Inactif,Cloturer',
        //     'photo' => 'nullable|url',
        //     'referentiels' => 'nullable|array',
        //     'referentiels.*.code' => 'required|string',
        //     'referentiels.*.libelle' => 'required|string',
        //     'referentiels.*.description' => 'nullable|string',
        //     'referentiels.*.photo' => 'nullable|url',
        //     'referentiels.*.competences' => 'nullable|array',
        //     'referentiels.*.apprenants' => 'nullable|array',
        //     'referentiels.*.apprenants.*.nom' => 'required|string',
        //     'referentiels.*.apprenants.*.prenom' => 'required|string',
        //     'referentiels.*.apprenants.*.adresse' => 'nullable|string',
        //     'referentiels.*.apprenants.*.email' => 'required|email',
        //     'referentiels.*.apprenants.*.password' => 'required|string|min:8',
        //     'referentiels.*.apprenants.*.telephone' => 'nullable|string',
        //     'referentiels.*.apprenants.*.role' => 'nullable|string',
        //     'referentiels.*.apprenants.*.statut' => 'nullable|string',
        //     'referentiels.*.apprenants.*.photo' => 'nullable|url',
        // ]);

        try {
            $result = $this->promotionService->createPromotion($request->all());
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function index(){
        try {
            $result = $this->promotionService->getAllPromotions();
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getActivePromotion(){
        try {
            $result = $this->promotionService->getActivePromotion();
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function cloturer($id)
    {
        $promotion = $this->promotionService->cloturer($id);
        return response()->json(['message' => 'La promotion a été clôturée avec succès. Les relevés de notes seront envoyés.']);
    }
    public function getReferentielsActifs($id){
        try {
            $result = $this->promotionService->getReferentielsActifs($id);
            return response()->json(["message"=>"Referentiles actifs trouvés","data"=>$result, "code"=>201]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getStatsPromos($id){
        try {
            $result = $this->promotionService->getStatsPromos($id);
            return response()->json(["message"=>"Statistiques des promotions trouvées","data"=>$result, "code"=>201]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function UpdateEtat(Request $request,$id){
        // $this->validate($request, [
        //     'etat' => 'in:Actif,Inactif,Cloturée',
        // ]);
        
        try {
             $promotion = $this->promotionService->updateEtat($request->etat,$id);
            return response()->json(["message"=>"État de la promotion modifié avec succès","data"=>  $promotion, "code"=>201]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
