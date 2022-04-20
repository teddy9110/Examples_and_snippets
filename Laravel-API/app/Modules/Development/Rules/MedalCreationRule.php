<?php

namespace Rhf\Modules\Development\Rules;

use Illuminate\Contracts\Validation\Rule;

class MedalCreationRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!isset($value['type'])) {
            return false;
        } else {
            return array_search(strtolower($value['type']), ['gold', 'silver', 'bronze']) !== false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Medal type is invalid';
    }
}
