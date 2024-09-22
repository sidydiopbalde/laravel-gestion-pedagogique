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

class ApprenantsFirebaseController extends Controller
{
    protected $apprenantsFirebaseService;

    public function __construct(ApprenantsFirebaseService $apprenantsFirebaseService)
    {
        $this->apprenantsFirebaseService = $apprenantsFirebaseService;
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:apprenants,email',
            // Ajoutez d'autres champs de validation selon la capture d'écran et vos besoins
        ]);

        $firebaseKey = $this->apprenantsFirebaseService->createApprenant($validated);
        return response()->json(['message' => 'Apprenant créé avec succès', 'id' => $firebaseKey]);
    }

    public function index()
    {
        $apprenants = $this->apprenantsFirebaseService->getAllApprenants();
        return response()->json(['apprenants' => $apprenants]);
    }
}