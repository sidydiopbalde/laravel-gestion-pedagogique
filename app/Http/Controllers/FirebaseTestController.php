<?php

namespace App\Http\Controllers;

use App\Services\ServiceFirebaseInterface;
use Illuminate\Http\Request;

class FirebaseTestController extends Controller
{
    protected $firebaseService;

    public function __construct(ServiceFirebaseInterface $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function createUser(Request $request)
    {
        $data = $request->only(['name', 'email']);
        $firebasePath = 'users'; // Chemin dans Firebase
        $firebaseId = $this->firebaseService->create($firebasePath, $data);

        return response()->json([
            'message' => 'User created successfully in Firebase',
            'firebaseId' => $firebaseId
        ], 201);
    }

    public function getUser($id)
    {
        $firebasePath = 'users'; // Chemin dans Firebase
        $user = $this->firebaseService->find($firebasePath, $id);

        if ($user) {
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function deleteUser($id)
    {
        $firebasePath = 'users'; // Chemin dans Firebase
        $this->firebaseService->delete($firebasePath, $id);

        return response()->json(['message' => 'User deleted successfully']);
    }
}
