<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreUserClientExistRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\User;
use PDF;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }



    
    public function store(StoreUserRequest $request)
    {
        
        $validatedData = $request->validated();
        // dd($validatedData);
        $user = $this->userService->createUser($validatedData);
        return $user;
    }

    public function exportExcel()
    {
        // Stocker le fichier Excel dans le répertoire storage/app/public
        Excel::store(new UsersExport, 'users.xlsx', 'public');
    
        // Télécharger le fichier après stockage
        return Excel::download(new UsersExport, 'users.xlsx');
    }
    
    // public function exportPdf()
    // {
    //     $users = User::all();
    //     $pdf = PDF::loadView('users.pdf', compact('users'));
    //     return $pdf->download('users.pdf');
    // }
   
  
  

}
