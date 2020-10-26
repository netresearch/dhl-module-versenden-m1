<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class AddressType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder\Person $requestData
     * @return VersendenApi\NativeAddressType
     */
    public static function prepare(RequestData $requestData)
    {
        $countryType = new VersendenApi\CountryType($requestData->getCountryISOCode());
        $countryType->setCountry($requestData->getCountry());
        $countryType->setState($requestData->getState());

        $requestType = new VersendenApi\NativeAddressType(
            $requestData->getStreetName(),
            $requestData->getStreetNumber(),
            $requestData->getZip(),
            $requestData->getCity(),
            null,
            $countryType
        );

        return $requestType;
    }
}
