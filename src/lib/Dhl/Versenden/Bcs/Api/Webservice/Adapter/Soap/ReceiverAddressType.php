<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class ReceiverAddressType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder\Receiver $requestData
     * @return VersendenApi\ReceiverNativeAddressType
     */
    public static function prepare(RequestData $requestData)
    {
        $countryType = new VersendenApi\CountryType($requestData->getCountryISOCode());
        $countryType->setCountry($requestData->getCountry());
        $countryType->setState($requestData->getState());

        $requestType = new VersendenApi\ReceiverNativeAddressType(
            $requestData->getName2(),
            $requestData->getName3(),
            $requestData->getStreetName(),
            $requestData->getStreetNumber(),
            $requestData->getZip(),
            $requestData->getCity(),
            null,
            $countryType
        );

        $requestType->setAddressAddition(array($requestData->getAddressAddition()));

        return $requestType;
    }
}
