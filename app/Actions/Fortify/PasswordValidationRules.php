<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Services\HibpService;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

trait PasswordValidationRules
{
    /**
     * @return array<int, string|Rule|ValidationRule|array<mixed>>
     */
    protected function passwordRules(): array
    {
        $min = (int) config('security.password.min_length', 12);
        $max = (int) config('security.password.max_length', 128);

        return [
            'required',
            'string',
            "min:{$min}",
            "max:{$max}",
            'confirmed',
            $this->pwnedPasswordRule(),
        ];
    }

    private function pwnedPasswordRule(): ValidationRule
    {
        return new class implements ValidationRule
        {
            public function validate(string $attribute, mixed $value, \Closure $fail): void
            {
                if (! is_string($value) || $value === '') {
                    return;
                }
                $hits = HibpService::fromConfig()->occurrencesOf($value);
                if ($hits > 0) {
                    $fail(__('Bu şifre bilinen veri sızıntılarında :count kez görülmüş. Lütfen farklı bir şifre seçin.', ['count' => number_format($hits)]));
                }
            }
        };
    }
}
