<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class DeleteShipmentRequestType implements RequestType
{
    /**
     * @param RequestData\DeleteShipment $requestData
     * @return VersendenApi\DeleteShipmentOrderRequest
     */
    public static function prepare(RequestData $requestData)
    {
        $version = new VersendenApi\Version(
            $requestData->getVersion()->getMajorRelease(),
            $requestData->getVersion()->getMinorRelease(),
            $requestData->getVersion()->getBuild()
        );

        $shipmentNumbers = $requestData->getShipmentNumbers();

        $requestType = new VersendenApi\DeleteShipmentOrderRequest(
            $version,
            $shipmentNumbers
        );

        return $requestType;
    }
}
