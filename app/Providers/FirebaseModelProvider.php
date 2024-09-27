<?php

namespace App\Providers;

use App\Models\ApprenantFirebaseModel;
use App\Models\FirebaseModel;
use App\Models\PromoFirebaseModel;
use App\Models\ReferentielFirebaseModel;
use App\Models\UserFirebaseModel;
use App\Repository\FirebaseRepositoryImpl;
use App\Repository\FirebaseRepositoryInterface;
use App\Repository\PromoFirebaseRepository;
use App\Repository\PromotionFirebaseRepositoryInterface;
use App\Repository\UserFirebaseRepository;
use App\Repository\UserFirebaseRepositoryInterface;
use App\Services\FirebaseService;
use App\Services\FirebaseServiceInterface;
use App\Services\PromotionFirebaseInterface;
use App\Services\PromotionFirebaseService;
use App\Services\UserFirebaseService;
use App\Services\UserFirebaseServiceInterface;
use Illuminate\Support\ServiceProvider;

class FirebaseModelProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Lier l'interface FirebaseServiceInterface à son implémentation FirebaseService
        $this->app->bind(FirebaseServiceInterface::class, FirebaseService::class);

        // Lier l'interface FirebaseRepository à son implémentation FirebaseRepositoryImpl
        $this->app->bind(FirebaseRepositoryInterface::class, function ($app) {
            // Créer une nouvelle instance de FirebaseModel
            $firebaseModel = new FirebaseModel(); // Assurez-vous que le noeud est bien défini dans le modèle

            return new FirebaseRepositoryImpl($firebaseModel);
        });

        // Singleton pour le service Firebase
        $this->app->singleton('firebaseService', function ($app) {
            return $app->make(FirebaseService::class);
        });

        // Singleton pour les modèles Firebase
        $this->app->singleton('user_firebase', function ($app) {
            return new UserFirebaseModel();
        });
        $this->app->singleton('promotion_firebase', function ($app) {
            return new PromoFirebaseModel();
        });
        $this->app->singleton('referentiel_firebase', function ($app) {
            return new ReferentielFirebaseModel();
        });
        $this->app->singleton('apprenants_firebase', function ($app) {
            return new ApprenantFirebaseModel();
        });
        // Singleton pour le dépôt et le service UserFirebase
        $this->app->singleton(UserFirebaseRepositoryInterface::class, function ($app) {
            return new UserFirebaseRepository();
        });
        $this->app->singleton(UserFirebaseServiceInterface::class, function ($app) {
            return new UserFirebaseService($app->make(UserFirebaseRepository::class));
        });
        $this->app->singleton(PromotionFirebaseInterface::class, function ($app) {
            return new PromotionFirebaseService($app->make(PromoFirebaseRepository::class));
        });
        $this->app->singleton(PromotionFirebaseRepositoryInterface::class, function ($app) {
            return new PromoFirebaseRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tout code à exécuter lors du bootstrap, si nécessaire.
    }
}
