<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneRules implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Regex pour valider le numéro de téléphone sénégalais
        $pattern = '/^(\\+221)?(77|78|76|75)[0-9]{7}$/';

        if (!preg_match($pattern, $value)) {
            $fail('Le numéro de téléphone n\'est pas valide.');
        }
    }
}
