<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthentificationServiceInterface;
use App\Services\AuthentificationPassport ;
use Kreait\Firebase\Factory;
use App\Services\AuthenticationSanctum;
use App\Services\AuthentificationFactory;
use App\Services\AuthentificationFirebase;
use Kreait\Firebase\Auth as FirebaseAuth;
class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AuthentificationServiceInterface::class, function ($app) {
            return new AuthentificationPassport();
        });
        $this->app->singleton(FirebaseAuth::class, function ($app) {
            return (new Factory)
                ->withServiceAccount([
                    'project_id' => env('FIREBASE_PROJECT_ID'),
                    'client_email' => env('FIREBASE_CLIENT_MAIL'),
                    'private_key' => env('FIREBASE_PRIVATE_KEY'),
                ])
                ->createAuth();
        });

      
        $this->app->singleton(AuthentificationFirebase::class, function ($app) {
            return new AuthentificationFirebase($app->make(FirebaseAuth::class));
        });
    }

    public function boot()
    {
        //
    }
}
