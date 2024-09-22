<?php

use App\Http\Controllers\ApprenantsFirebaseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\FirebaseTestController;
use App\Http\Controllers\FirebaseUserController;
use App\Http\Controllers\PromotionFirebaseController;
use App\Http\Controllers\ReferentielFirebaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFirebaseController;
use App\Http\Middleware\PromotionStatutMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::post('/v1/login', [FirebaseAuthController::class, 'authenticateWithCredentials']);

// Route::get('users', [UserController::class, 'index']);
//Route::get('users/export/excel', [UserController::class, 'exportExcel']);
// Route::get('users/export/pdf', [UserController::class, 'exportPdf']);

//--------------------TESTFirebase--------------------------------
// Route::post('/firebase/user', [FirebaseTestController::class, 'createUser']);
// Route::get('firebase/user/{id}', [FirebaseTestController::class, 'getUser']);
// Route::delete('firebase/user/{id}', [FirebaseTestController::class, 'deleteUser']);

// Route::post('/auth/google', [FirebaseAuthController::class, 'authenticateGoogle']);
// // routes/web.php
// Route::get('/firebase/test', [FirebaseController::class, 'store']);
// // Route::post('/v1/users', [UserController::class, 'store']);
// Route::post('/v1/login', [AuthController::class, 'login']);
 //Route::post('/firebase/auth', [FirebaseAuthController::class, 'authenticateWithCredentials']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('auth/login', [UserController::class, 'login']);
// Route::post('/v1/users', [FirebaseUserController::class, 'store']);
// Route::get('users', [FirebaseUserController::class, 'index']);
// Route::patch('users/{id}', [FirebaseUserController::class, 'update']);
Route::prefix('/v1/users')->group(function () {
    Route::post('/firebase', [UserFirebaseController::class, 'create']);
    Route::put('/{id}', [UserFirebaseController::class, 'update']);
    Route::patch('/{id}', [UserFirebaseController::class, 'update']);
    Route::delete('/{id}', [UserFirebaseController::class, 'delete']);
    Route::get('/{id}', [UserFirebaseController::class, 'find']);
    Route::get('/', [UserFirebaseController::class, 'getAll']);
});
Route::post('/v1/login', [AuthController::class, 'login']);
Route::get('users/export/excel', [UserController::class, 'exportExcel']);

// //-------------------REFERENTIELS--------------------------------
// Routes pour les référentiels
Route::prefix('/v1/referentiels')->group(function () {
    Route::get('/', [ReferentielFirebaseController::class, 'index']);
    Route::post('/', [ReferentielFirebaseController::class, 'store']);
    Route::get('/{id}', [ReferentielFirebaseController::class, 'show']);
    Route::put('/{id}', [ReferentielFirebaseController::class, 'update']);
    Route::delete('/{id}', [ReferentielFirebaseController::class, 'delete']);
    
    // Ajouter une compétence à un référentiel
    Route::post('/{referentielId}/competences', [ReferentielFirebaseController::class, 'addCompetence']);
    
    // Supprimer une compétence (avec soft delete) et ses modules
    Route::delete('/{referentielId}/competences/{competenceId}', [ReferentielFirebaseController::class, 'deleteCompetence']);
    
    // Ajouter des modules à une compétence
    Route::post('/{referentielId}/competences/{competenceId}/modules', [ReferentielFirebaseController::class, 'addModules']);
    
    // Supprimer un module d'une compétence
    Route::delete('/{referentielId}/competences/{competenceId}/modules/{moduleId}', [ReferentielFirebaseController::class, 'deleteModule']);
});
Route::get('/v1/archive/referentiel', [ReferentielFirebaseController::class, 'getArchivedReferentiels']);

Route::prefix('/v1/promotions')->group(function () {
    Route::post('/', [PromotionFirebaseController::class, 'store']);
     Route::get('/', [PromotionFirebaseController::class, 'index']);
     Route::get('/encours', [PromotionFirebaseController::class,'getActivePromotion']);
     
     
     
     // Appliquer le middleware pour bloquer les actions après la clôture
     Route::middleware([PromotionStatutMiddleware::class])->group(function () {
         Route::put('/{id}', [PromotionFirebaseController::class, 'update']);
         Route::delete('/{id}', [PromotionFirebaseController::class, 'delete']);
         Route::patch('/{id}/cloturer', [PromotionFirebaseController::class, 'cloturer']);
        });
        Route::patch('/{id}/etat', [PromotionFirebaseController::class, 'UpdateEtat']);

    Route::get('/{id}/referentiels', [PromotionFirebaseController::class, 'getReferentielsActifs']);
    Route::get('/{id}/stats', [PromotionFirebaseController::class, 'getStatsPromos']);
});


// Route::get('/v1/promotions/{id}/apprenants', [PromotionFirebaseController::class, 'getApprenants']);
Route::prefix('/v1/apprenants')->group(function () {
Route::post('/apprenants', [ApprenantsFirebaseController::class, 'store']);
Route::get('apprenants', [ApprenantsFirebaseController::class, 'index']);
});
//-----------ROLES--------------------------------
// Route::patch('/v1/promotions/{id}/etat', [PromotionFirebaseController::class, 'changeStatus'])
//     ->middleware('checkRole:Manager');

