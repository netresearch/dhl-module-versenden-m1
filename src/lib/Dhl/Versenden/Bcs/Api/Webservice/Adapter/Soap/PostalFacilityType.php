<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

class PostalFacilityType implements RequestType
{
    /**
     * @param Receiver\PostalFacility $requestData
     * @return VersendenApi\PackstationType|VersendenApi\PostfilialeType
     */
    public static function prepare(RequestData $requestData = null)
    {
        $postalFacilityType = null;
        if (!$requestData instanceof Receiver\PostalFacility) {
            return $postalFacilityType;
        }

        $countryType = new VersendenApi\CountryType($requestData->getCountryISOCode());
        $countryType->setCountry($requestData->getCountry());
        $countryType->setState($requestData->getState());

        if ($requestData instanceof Receiver\Packstation) {
            $postalFacilityType = new VersendenApi\PackStationType(
                $requestData->getPackstationNumber(),
                $requestData->getZip(),
                $requestData->getCity(),
                null,
                $countryType
            );
            $postalFacilityType->setPostNumber($requestData->getPostNumber());
        } elseif ($requestData instanceof Receiver\Postfiliale) {
            $postalFacilityType = new VersendenApi\PostfilialeType(
                $requestData->getPostfilialNumber(),
                $requestData->getPostNumber(),
                $requestData->getZip(),
                $requestData->getCity()
            );
        }

        return $postalFacilityType;
    }
}
