<?php

namespace App\Providers;

use App\Repositories\ReferentielFirebaseRepository;
use App\Repository\FirebaseRepositoryImpl;
use App\Repository\FirebaseRepositoryInterface;
use App\Repository\ReferentielFirebaseRepositoryInterface;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryImpl;
use App\Services\ReferentielFirebaseService;
use App\Services\ReferentielFirebaseServiceInterface;
use App\Services\ServiceFirebase;
use App\Services\UserService;
use App\Services\UserServiceImpl;
use Illuminate\Support\ServiceProvider;
use App\Models\FirebaseModel;
use App\Repository\ReferentielFirebaseRepository as RepositoryReferentielFirebaseRepository;
use App\Services\ServiceFirebaseInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\FirebaseServiceInterface::class,
            \App\Services\FirebaseService::class
        );
        $this->app->singleton(UserRepository::class, function ($app) {
            return new UserRepositoryImpl();
        });
        $this->app->bind(FirebaseRepositoryInterface::class, function ($app) {
            // Utilisation d'une instance de FirebaseModel avec un noeud par défaut ou passé dynamiquement
            $firebaseModel = new FirebaseModel();

            return new FirebaseRepositoryImpl($firebaseModel);
        });
        $this->app->singleton(UserService::class, function ($app) {
            return new UserServiceImpl($app->make(UserRepository::class));
        });
        // $this->app->bind(FirebaseServiceInterface::class, FirebaseServiceImpl::class);

        // // Alias pour la façade
        $this->app->singleton('firebaseService', function ($app) {
            return $app->make(ServiceFirebase::class);
        });
        $this->app->singleton(ServiceFirebaseInterface::class, function ($app) {
            return new ServiceFirebase();
        });
        $this->app->bind(ReferentielFirebaseRepositoryInterface::class, RepositoryReferentielFirebaseRepository::class);
        $this->app->bind(ReferentielFirebaseService::class, function ($app) {
            return new ReferentielFirebaseService($app->make(ReferentielFirebaseRepositoryInterface::class));
        });
        $this->app->bind(ReferentielFirebaseServiceInterface::class, ReferentielFirebaseService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
