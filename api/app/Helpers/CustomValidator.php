<?php

namespace App\Helpers;
use Illuminate\Validation\Validator;


class CustomValidator extends Validator
{

    /**
     * Function to validate input lat long
     *
     * Check if input lat long are in array and as in string format and valid lat long values.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     *
     * @return bool
     */

    public function validateOrderLatLong($attribute, $value, $parameters)
    {
        if( is_array($value) && count($value) == 2 && is_string($value[0]) && is_string($value[1])) {
            if (
                preg_match(config('order.lat_regex'), $value[0]) &&
                preg_match(config('order.long_regex'), $value[1]) &&
                $value[0] != $value[1]
            ) {
                return true;
            }
        }
        return false;
    }
}


