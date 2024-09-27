<?php

use App\Http\Controllers\ApprenantsFirebaseController;
use App\Http\Controllers\ApprennantsFirebaseController;
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



Route::prefix('/v1/users')->group(function () {
    Route::post('/firebase', [UserFirebaseController::class, 'create']);
    Route::put('/{id}', [UserFirebaseController::class, 'update']);
    Route::patch('/{id}', [UserFirebaseController::class, 'update']);
    Route::delete('/{id}', [UserFirebaseController::class, 'delete']);
    Route::get('/{id}', [UserFirebaseController::class, 'find']);
    Route::get('/', [UserFirebaseController::class, 'getAll']);
});
Route::post('/v1/login', [AuthController::class, 'login'])->name('login');
Route::get('users/export/excel', [UserController::class, 'exportExcel']);


Route::prefix('/v1/referentiels')->group(function () {
  
    Route::post('/', [ReferentielFirebaseController::class, 'store']);
    Route::get('/{id}', [ReferentielFirebaseController::class, 'show']);
    Route::put('/{id}', [ReferentielFirebaseController::class, 'update']);
    Route::delete('/{id}', [ReferentielFirebaseController::class, 'delete']);
    
    // Ajouter une compétence à un référentiel
    Route::post('/{referentielId}/competences', [ReferentielFirebaseController::class, 'addCompetence']);
    Route::delete('/{referentielId}/competences/{competenceId}', [ReferentielFirebaseController::class, 'deleteCompetence']);
    Route::post('/{referentielId}/competences/{competenceId}/modules', [ReferentielFirebaseController::class, 'addModules']);
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
Route::post('/', [ApprennantsFirebaseController::class, 'store']);
Route::post('/import', [ApprennantsFirebaseController::class, 'import']);
Route::post('/relance', [ApprennantsFirebaseController::class, 'sendGroupRelance']);
Route::get('/', [ApprennantsFirebaseController::class, 'filterApprenants']);
Route::get('/{id}', [ApprennantsFirebaseController::class, 'show']);
// Route::post('/inactive', [ApprennantsFirebaseController::class, 'findApprenantInactif']);

});
// Route::middleware(['switch.auth'])->group(function () {
//     Route::get('/v1/referentiels', [ReferentielFirebaseController::class, 'index']);
//     Route::get('/profile', 'UserController@profile');
//     Route::get('/dashboard', 'AdminController@dashboard');
// });

Route::middleware('firebase')->group(function () {
    Route::post('/apprenants/relance', [ApprennantsFirebaseController::class, 'relance']);
    Route::post('/apprenants/{id}/relance', [ApprennantsFirebaseController::class, 'relanceById']);
});

Route::middleware('auth:api')->group(function () {
    // Route::patch('/promotions/{id}', [PromotionController::class, 'update'])
    //     ->can('access', 'App\Models\Promotion');
    
    // Route::patch('/promotions/{id}/etat', [PromotionController::class, 'updateEtat'])
    //     ->can('access', 'App\Models\Promotion');
    Route::post('/v1/apprenants/inactive', [ApprennantsFirebaseController::class, 'findApprenantInactif'])->can('access', 'App\Models\Promotion');;
});