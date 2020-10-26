<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class NameType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder\Person $requestData
     * @return VersendenApi\NameType
     */
    public static function prepare(RequestData $requestData)
    {
        $requestType = new VersendenApi\NameType(
            $requestData->getName1(),
            $requestData->getName2(),
            $requestData->getName3()
        );

        return $requestType;
    }
}
