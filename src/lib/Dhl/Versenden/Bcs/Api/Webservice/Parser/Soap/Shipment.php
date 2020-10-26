<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

abstract class Shipment implements Parser
{
    /**
     * @param \stdClass $response
     * @return \stdClass
     */
    abstract public function parse($response);

    /**
     * @param VersendenApi\Statusinformation $statusInfo
     * @return ResponseData\Status\Response
     */
    protected function parseResponseStatus(VersendenApi\Statusinformation $statusInfo)
    {
        $status = new ResponseData\Status\Response(
            $statusInfo->getStatusCode(),
            $statusInfo->getStatusText(),
            (array) $statusInfo->getStatusMessage()
        );
        return $status;
    }

    /**
     * @param string $itemId Sequence number or shipment number
     * @param VersendenApi\Statusinformation $statusInfo
     * @return ResponseData\Status\Item
     */
    protected function parseItemStatus($itemId, VersendenApi\Statusinformation $statusInfo)
    {
        $status = new ResponseData\Status\Item(
            $itemId,
            $statusInfo->getStatusCode(),
            $statusInfo->getStatusText(),
            $statusInfo->getStatusMessage()
        );
        return $status;
    }
}
