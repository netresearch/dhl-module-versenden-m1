<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice;

abstract class RequestData
{
    /**
     * Validate a config setting's string length.
     *
     * @param string $name The label/key
     * @param string $value The value to be validated
     * @param int $minLength The minimum allowed string length
     * @param int $maxLength The maximum allowed string length
     * @return bool
     * @throws RequestData\ValidationException
     */
    public function validateLength($name, $value, $minLength, $maxLength)
    {
        if ( ($minLength > 0) && ($value == "") ) {
            throw new RequestData\ValidationException("$name is a required value.");
        }

        if (strlen($value) < $minLength) {
            throw new RequestData\ValidationException("Please enter at least $minLength characters for $name.");
        }

        if (strlen($value) > $maxLength) {
            throw new RequestData\ValidationException("Please enter no more than $maxLength characters for $name.");
        }

        return true;
    }
}
