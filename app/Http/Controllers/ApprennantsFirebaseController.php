<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\ApprenantsFirebaseService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exports\UserFirebaseExport;
use Maatwebsite\Excel\Facades\Excel;

class ApprennantsFirebaseController extends Controller
{
    protected $apprenantsFirebaseService;

    public function __construct(ApprenantsFirebaseService $apprenantsFirebaseService)
    {
        $this->apprenantsFirebaseService = $apprenantsFirebaseService;
    }
    public function store(Request $request)
    {
        $firebaseKey = $this->apprenantsFirebaseService->createApprenant($request->all());
        return response()->json(['message' => 'Apprenant créé avec succès', 'id' => $firebaseKey]);
    }
    public function import(Request $request)
    {
    
        $file = $request->file('file');
        $referentielId = $request->input('referentiel_id');
        $failedApprenantsFile = $this->apprenantsFirebaseService->importApprenants($file, $referentielId);
        return response()->download($failedApprenantsFile)->deleteFileAfterSend(true);
    }
    public function index()
    {
        $apprenants = $this->apprenantsFirebaseService->getAllApprenants();
        return response()->json(['apprenants' => $apprenants]);
    }
    public function filterApprenants()
    {
        $filters = request()->query('filtre', []); // Utiliser un tableau vide par défaut
        $apprenants = $this->apprenantsFirebaseService->filterApprenants($filters);
        return response()->json(['apprenants' => $apprenants]);
    }
    public function show($id)
    {
        $filters = request()->query('filtre', []);
        $apprenant = $this->apprenantsFirebaseService->findApprenantBy_ID($id,$filters);
        if (!$apprenant) {
            return response()->json(['error' => 'Apprenant non trouvé'], 404);
        }
        return response()->json(['apprenant' => $apprenant]);
    }
    public function findApprenantInactif()
    {
        $apprenantsInactifs = $this->apprenantsFirebaseService->findApprenantsInactif();
        return response()->json(['apprenants_inactifs' => $apprenantsInactifs]);
    }
    public function sendGroupRelance()
    {
        $this->apprenantsFirebaseService->sendGroupRelance();
        return response()->json(['Status' => 'Success','message' => 'Relances envoyées avec succès à tous les apprenants non activés.']);
    }

}