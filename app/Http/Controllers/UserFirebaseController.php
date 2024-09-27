<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserFirebaseService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exports\UserFirebaseExport;
use Maatwebsite\Excel\Facades\Excel;
class UserFirebaseController extends Controller
{
    protected $userFirebaseService;

    public function __construct(UserFirebaseService $userFirebaseService)
    {
        $this->userFirebaseService = $userFirebaseService;
    }

    /**
     * Méthode pour créer un utilisateur avec l'upload de photo dans Firebase Storage
     */
    public function create(Request $request)
    {
        $data = $request->all();
    
        // Vérification si l'utilisateur existe déjà dans Firebase par email ou téléphone
        $existingEmailUser = $this->userFirebaseService->findUserByEmail($data['email']);
        $existingPhoneUser = $this->userFirebaseService->findUserByPhone($data['telephone']);
    
        if ($existingEmailUser) {
            return response()->json(['error' => 'Cet email existe déjà dans Firebase.'], 422);
        }
    
        if ($existingPhoneUser) {
            return response()->json(['error' => 'Ce numéro de téléphone existe déjà dans Firebase.'], 422);
        }
    
        // Gestion de l'upload de la photo si nécessaire
        if ($request->hasFile('photo')) {
            $fileName = 'photos/' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
            $filePath = $request->file('photo')->getPathname();
    
            $photoUrl = $this->userFirebaseService->uploadImageToStorage($filePath, $fileName);
    
            if ($photoUrl) {
                $data['photo'] = $photoUrl;
            } else {
                return response()->json(['error' => 'Erreur lors de l\'upload de la photo.'], 500);
            }
        }
    
        // Hash du mot de passe
        $data['password'] = Hash::make($data['password']);
    
        $userIdFirebase = null;
    
        DB::transaction(function () use ($data, $request, &$userIdFirebase,$filePath) {
            // Créer l'utilisateur dans Firebase
            $userIdFirebase = $this->userFirebaseService->createUserWithEmailAndPassword($data['email'], $request->password);
          
    
            // Assigner des claims personnalisés (comme le rôle) à l'utilisateur Firebase
            $this->userFirebaseService->setCustomUserClaims($userIdFirebase, ['role' => $data['role']]);
            $randomId = random_int(100000, 999999);

            $userData = [
                'nom' => $data['nom'],
                'id' => $randomId,
                'prenom' => $data['prenom'],
                'adresse' => $data['adresse'],
                'telephone' => $data['telephone'],
                'fonction_id' => $data['fonction_id'],
                'role' => $data['role'],
                'email' => $data['email'],
                'photo' => $data['photo'],
                'statut' => $data['statut'] ?? 'Actif',
            ];
    
            $this->userFirebaseService->createUser($userData);
            User::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'adresse' => $data['adresse'],
                'telephone' => $data['telephone'],
                'fonction_id' => $data['fonction_id'],
                'email' => $data['email'],
                'photo' => $filePath,
                'statut' => $data['statut'] ?? 'Actif',
                'password' => $data['password'],
                'firebase_uid' => $userIdFirebase,  // Enregistrer l'UID Firebase ici
            ]);
        });
    
        return response()->json(['userIdFirebase' => $userIdFirebase, 'message' => 'Utilisateur créé avec succès.'], 201);
    }
    
    

    /**
     * Méthode pour récupérer tous les utilisateurs
     */
    public function getAll(Request $request)
    {
        $fonctionId = $request->query('role'); // Récupérer le paramètre de fonction_id
    
        // Appel au service pour récupérer tous les utilisateurs
        $usersFirebase = $this->userFirebaseService->getAllUsers();
    
        // Convertir en collection si ce n'est pas déjà une
        $usersFirebase = collect($usersFirebase);
    
        // Si un fonction_id est spécifié, filtrer les utilisateurs
        if ($fonctionId) {
            $usersFirebase = $usersFirebase->filter(function($user) use ($fonctionId) {
                return $user['fonction_id'] === $fonctionId; // Filtrer par fonction_id
            });
        }
    
        return response()->json($usersFirebase->values(), 200); // Utiliser values() pour réindexer
    }
    
    /**
     * Méthode pour update un utilisateur par son ID
     */
    public function update(Request $request, $id)
    {
        // Récupérer les données de la requête
        $data = $request->all();
        
        // Vérifier si l'utilisateur existe
        $existingUser = $this->userFirebaseService->findUser($id);
        if (!$existingUser) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }
    
        // Vérifier l'unicité de l'email et du téléphone si modifiés
        if (isset($data['email'])) {
            $existingEmailUser = $this->userFirebaseService->findUserByEmail($data['email']);
            if ($existingEmailUser && $existingEmailUser['id'] !== $id) {
                return response()->json(['error' => 'Cet email existe déjà dans Firebase.'], 422);
            }
        }
    
        if (isset($data['telephone'])) {
            $existingPhoneUser = $this->userFirebaseService->findUserByPhone($data['telephone']);
            if ($existingPhoneUser && $existingPhoneUser['id'] !== $id) {
                return response()->json(['error' => 'Ce numéro de téléphone existe déjà dans Firebase.'], 422);
            }
        }
    
        // Gestion de la photo si fournie
        if ($request->hasFile('photo')) {
            $fileName = 'photos/' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
            $filePath = $request->file('photo')->getPathname();
    
            // Upload de la photo
            $photoUrl = $this->userFirebaseService->uploadImageToStorage($filePath, $fileName);
    
            if ($photoUrl) {
                $data['photo'] = $photoUrl; // Mettre à jour avec l'URL de la photo
            } else {
                return response()->json(['error' => 'Erreur lors de l\'upload de la photo.'], 500);
            }
        }
    
        // Mise à jour de l'utilisateur dans Firebase
        $userData = array_filter($data, function($value) {
            return !is_null($value); // Ignore les valeurs nulles
        });
        $this->userFirebaseService->updateUser($id, $userData);
    
        // Mise à jour dans la base de données locale
        User::where('id', $id)->update($userData);
    
        return response()->json(['message' => 'Utilisateur mis à jour avec succès.'], 200);
    }
    
} 
    
    //     public function exportExcel(Request $request)
    // {
    //     $fonctionId = $request->query('role'); // Récupérer le paramètre de fonction_id
    
    //     // Appel au service pour récupérer tous les utilisateurs
    //     $usersFirebase = $this->userFirebaseService->getAllUsers();
    
    //     // Convertir en collection si ce n'est pas déjà une
    //     $usersFirebase = collect($usersFirebase);
    
    //     // Si un fonction_id est spécifié, filtrer les utilisateurs
    //     if ($fonctionId) {
    //         $usersFirebase = $usersFirebase->filter(function($user) use ($fonctionId) {
    //             return $user['fonction_id'] === $fonctionId; // Filtrer par fonction_id
    //         });
    //     }
    
    //     // Exporter les utilisateurs dans un fichier Excel
    //     return Excel::download(new UserFirebaseExport($usersFirebase), 'users-firebase.xlsx');


