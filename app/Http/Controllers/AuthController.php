<?php
namespace App\Http\Controllers;

use App\Services\AuthentificationFactory;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthentificationFactory $authFactory)
    {
        // Utiliser la factory pour obtenir le service d'authentification approprié
        $this->authService = $authFactory->make();
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // dd($credentials);
        // Appeler la méthode authenticate() du service sélectionné
        $result = $this->authService->authenticate($credentials);

        return response()->json($result);
    }

    public function logout()
    {
        // Appeler la méthode logout() du service sélectionné
        $this->authService->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }
}

// namespace App\Http\Controllers;
// use App\Models\User;
// use App\Services\AuthenticationServiceInterface;
// use App\Services\AuthentificationServiceInterface;
// use Illuminate\Http\Request;

// class AuthController extends Controller
// {
//     protected $authService;

//     public function __construct(AuthentificationServiceInterface $authService)
//     {
//         $this->authService = $authService;
//     }

//     public function login(Request $request)
//     {
//         $credentials = $request->only('email', 'password');
//         $token = $this->authService->authenticate($credentials);

//         if ($token) {
//             return response()->json(['token' => $token], 200);
//         }

//         return response()->json(['error' => 'Unauthorized'], 401);
//     }
  
//     public function logout()
//     {
//         $this->authService->logout();
//         return response()->json(['message' => 'Successfully logged out'], 200);
//     }
// }
