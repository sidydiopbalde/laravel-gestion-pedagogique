<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ApprenantsFirebaseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'apprenants_firebase'; 
    }
}
