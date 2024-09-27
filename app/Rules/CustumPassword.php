<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password as LaravelPassword;
use Illuminate\Support\Facades\Validator;
class CustumPassword implements ValidationRule
{
    protected $passwordRule;

    public function __construct()
    {
        $this->passwordRule = LaravelPassword::min(8) // Longueur minimale
            ->mixedCase() // Lettre minuscule et majuscule
            ->numbers()   // Chiffre
            ->symbols();  // Caractère spécial
    }

    /**
     * Exécuter la règle de validation.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Utiliser les règles de Laravel pour valider le mot de passe
        $validator = Validator::make([$attribute => $value], [
            $attribute => $this->passwordRule,
        ]);

        if ($validator->fails()) {
            $fail($validator->errors()->first());
        }
    }
}
