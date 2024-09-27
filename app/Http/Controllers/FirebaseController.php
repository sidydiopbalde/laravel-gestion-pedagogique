<?php


namespace App\Http\Controllers;

use App\Services\FirebaseServiceInterface;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseServiceInterface $firebaseService)
    {
        $this->firebaseService = $firebaseService; // Conserver le service complet
    }

    public function index()
    {
        $reference = $this->firebaseService->getDatabase()->getReference('test'); // Specify your data path
        $snapshot = $reference->getSnapshot();
        $value = $snapshot->getValue();

        return response()->json($value); // Return the data as JSON
    }

    public function store(Request $request)
    {
        // Utilisation de la méthode create du service
        $path = 'users'; // Specify your data path
        $newData = $this->firebaseService->create($path, $request->all());

        return response()->json(['message' => 'Data created successfully', 'firebaseId' => $newData], 201);
    }
}

// namespace App\Http\Controllers;

// use App\Services\FirebaseServiceInterface;
// use Illuminate\Http\Request;

// class FirebaseController extends Controller
// {
//     protected $firebase;

//     public function __construct(FirebaseServiceInterface $firebase)
//     {
//         $this->firebase = $firebase->getDatabase();
//     }

//     public function index()
//     {
//         $reference = $this->firebase->getReference('test'); // Specify your data path
//         $snapshot = $reference->getSnapshot();
//         $value = $snapshot->getValue();

//         return response()->json($value); // Return the data as JSON
//     }

//     public function store(Request $request)
//     {
//         $reference = $this->firebase->getReference('test2'); // Specify your data path
//         $newData = $reference->push($request->all());
//         return response()->json($newData->getValue());
//     }
// }
