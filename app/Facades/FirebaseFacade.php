<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FirebaseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebaseService'; 
    }
}
