<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsHtmlRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $pattern = "/<(script|php|style|head|html|pre|video|href|iframe) ?.*>(.*)<\/(script|php|style|head|html|pre|video|href|iframe)>/";
        preg_match($pattern, $value, $matches);
        $containHtml = preg_match('/<\s?[^\>]*\/?\s?>/i', $value);
        if(!empty($matches) && $containHtml){
            $fail('The :attribute contains invalid characters.');
        }

    }
}