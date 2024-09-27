<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFirebase;
use App\Models\UserFirebaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirebaseUserController extends Controller
{
    public function __construct()
    {
        // Vous pouvez initialiser des dépendances ici si nécessaire
    }

    public function store(Request $request)
    {
        // Validation des données
        $this->validate($request, [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'fonction_id' => 'required|exists:fonctions,id', // Assurez-vous que la fonction existe
            'email' => 'required|email|unique:users',
            'photo' => 'nullable|string', // URL ou base64 de la photo
            'statut' => 'required|string|in:Actif,Bloquer',
        ]);

        DB::beginTransaction();

        try {
            // Création de l'utilisateur dans MySQL/PostgreSQL
            $newUser = User::create([
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'adresse' => $request->input('adresse'),
                'telephone' => $request->input('telephone'),
                'fonction_id' => $request->input('fonction_id'),
                'email' => $request->input('email'),
                'photo' => $request->input('photo'),
                'statut' => $request->input('statut'),
            ]);

            // Préparer les données pour Firebase
            $firebaseData = $newUser->toArray();

            // Utiliser le modèle UserFirebase pour interagir avec Firebase
            // $userFirebase = new UserFirebaseModel();
            // $firebasePath = 'users';
            // $firebaseId = $userFirebase->create($firebaseData); // Crée l'utilisateur dans Firebase

            // DB::commit(); // Confirme les changements dans MySQL/PostgreSQL

            // return response()->json([
            //     'message' => 'User created successfully in MySQL and Firebase',
            //     'user' => $newUser,
            //     'firebaseId' => $firebaseId,
            // ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Annule les changements dans MySQL/PostgreSQL si une erreur se produit
            return response()->json([
                'error' => 'Failed to create user: '.$e->getMessage(),
            ], 500);
        }
    }
}

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use App\Services\ServiceFirebaseInterface;
// use Illuminate\Support\Facades\DB;

// class FirebaseUserController extends Controller
// {
//     protected $firebaseService;

//     public function __construct(ServiceFirebaseInterface $firebaseService)
//     {
//         $this->firebaseService = $firebaseService;
//     }

//     public function store(Request $request)
//     {
//         // Validation des données selon la structure de la table
//         $this->validate($request, [
//             'nom' => 'required|string',
//             'prenom' => 'required|string',
//             'adresse' => 'required|string',
//             'telephone' => 'required|string',
//             'fonction_id' => 'required|exists:fonctions,id', // Assurez-vous que la fonction existe
//             'email' => 'required|email|unique:users',
//             'photo' => 'nullable|string', // URL ou base64 de la photo
//             'statut' => 'required|string|in:Actif,Bloquer',
//         ]);
    
//         DB::beginTransaction();
    
//         try {
//             // Création de l'utilisateur dans MySQL/PostgreSQL
//             $newUser = User::create([
//                 'nom' => $request->input('nom'),
//                 'prenom' => $request->input('prenom'),
//                 'adresse' => $request->input('adresse'),
//                 'telephone' => $request->input('telephone'),
//                 'fonction_id' => $request->input('fonction_id'),
//                 'email' => $request->input('email'),
//                 'photo' => $request->input('photo'),
//                 'statut' => $request->input('statut'),
//             ]);
    
//             // Préparation des données pour Firebase
//             $firebaseData = [
//                 'nom' => $newUser->nom,
//                 'prenom' => $newUser->prenom,
//                 'adresse' => $newUser->adresse,
//                 'telephone' => $newUser->telephone,
//                 'fonction_id' => $newUser->fonction_id,
//                 'email' => $newUser->email,
//                 'photo' => $newUser->photo,
//                 'statut' => $newUser->statut,
//             ];
    
//             $firebasePath = 'users';
//             $firebaseId = $this->firebaseService->create($firebasePath, $firebaseData);
    
//             DB::commit(); // Confirme les changements dans MySQL/PostgreSQL
    
//             return response()->json([
//                 'message' => 'User created successfully in MySQL and Firebase',
//                 'user' => $newUser,
//                 'firebaseId' => $firebaseId,
//             ], 201);
    
//         } catch (\Exception $e) {
//             DB::rollBack(); // Annule les changements dans MySQL/PostgreSQL si une erreur se produit
//             return response()->json([
//                 'error' => 'Failed to create user: '.$e->getMessage(),
//             ], 500);
//         }
//     }
// }



// namespace App\Http\Controllers;

// use App\Repository\FirebaseRepositoryInterface;
// use App\Models\FirebaseModel;
// use App\Repository\FirebaseRepositoryImpl;
// use Illuminate\Http\Request;

// class FirebaseUserController extends Controller
// {
//     protected $firebaseRepository;

//     public function __construct(FirebaseRepositoryInterface $firebaseRepository)
//     {
//         $this->firebaseRepository = $firebaseRepository;
//     }

//     public function store(Request $request)
//     {
//         $node = 'users'; // Exemple de noeud dynamique, peut être déterminé par la logique
//         $firebaseModel = new FirebaseModel(['firebaseNode' => $node]);

//         // Remplacez le repository avec l'instance appropriée
//         $this->firebaseRepository = new FirebaseRepositoryImpl($firebaseModel);

//         $data = $request->only(['name', 'email', 'role']);
//         $firebaseId = $this->firebaseRepository->create($data);

//         return response()->json([
//             'message' => 'User created successfully in Firebase',
//             'firebaseId' => $firebaseId
//         ], 201);
//     }
// }