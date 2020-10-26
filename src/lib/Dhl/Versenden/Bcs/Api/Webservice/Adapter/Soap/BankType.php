<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;
use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class BankType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder\Shipper\BankData $requestData
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public static function prepare(RequestData $requestData)
    {
        $requestType = new VersendenApi\BankType(
            $requestData->getAccountOwner(),
            $requestData->getBankName(),
            $requestData->getIban()
        );

        $requestType->setNote1($requestData->getNote1());
        $requestType->setNote2($requestData->getNote2());
        $requestType->setBic($requestData->getBic());
        $requestType->setAccountreference($requestData->getAccountReference());

        return $requestType;
    }
}
