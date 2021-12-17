<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

/**
 * poll liquied és secret összefüggés ellenörzése
 */
class LiquedRule implements Rule
{
    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct() {
    }

    /**
     * Determine if the validation rule passes.
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $liquied)  {
        $secret = \Request::input('secret');
        if (($secret == true) & ($liquied == true)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('poll.liquiedSecretError');
    }
}
