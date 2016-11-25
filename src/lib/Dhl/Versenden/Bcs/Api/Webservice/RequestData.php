<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice;

/**
 * RequestData
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
