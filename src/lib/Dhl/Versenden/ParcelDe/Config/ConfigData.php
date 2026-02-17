<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Config;

abstract class ConfigData
{
    /**
     * Validate a config setting's string length.
     *
     * @param string $name The label/key
     * @param string $value The value to be validated
     * @param int $minLength The minimum allowed string length
     * @param int $maxLength The maximum allowed string length
     * @return bool
     * @throws ValidationException
     */
    public function validateLength($name, $value, $minLength, $maxLength)
    {
        // Cast to string to handle null safely for PHP 8.1 compatibility
        $value = (string) $value;

        if (($minLength > 0) && ($value == '')) {
            throw new ValidationException("$name is a required value.");
        }

        if (strlen($value) < $minLength) {
            throw new ValidationException("Please enter at least $minLength characters for $name.");
        }

        if (strlen($value) > $maxLength) {
            throw new ValidationException("Please enter no more than $maxLength characters for $name.");
        }

        return true;
    }
}
