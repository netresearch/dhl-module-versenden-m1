<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class CommunicationType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder\Person $requestData
     * @return VersendenApi\CommunicationType
     */
    public static function prepare(RequestData $requestData)
    {
        $requestType = new VersendenApi\CommunicationType();
        $requestType->setContactPerson($requestData->getContactPerson());
        $requestType->setEmail($requestData->getEmail());
        $requestType->setPhone($requestData->getPhone());

        return $requestType;
    }
}
