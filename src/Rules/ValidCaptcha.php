<?php

namespace Elysdan\EMCaptcha\Rules;

use Closure;
use Elysdan\EMCaptcha\EMCaptchaManager;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $manager = app(EMCaptchaManager::class);

        if (! $manager->check($value)) {
            $fail('La respuesta del captcha es incorrecta.');
        }
    }
}
