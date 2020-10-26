<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap;

use \Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser;
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

abstract class ShipmentLabel extends Shipment implements Parser
{
    /**
     * @param VersendenApi\CreationState $state
     * @return ResponseData\CreateShipment\Label
     */
    protected function parseLabel(VersendenApi\CreationState $state)
    {
        $labelStatus = new ResponseData\Status\Item(
            $state->getSequenceNumber(),
            $state->getLabelData()->getStatus()->getStatusCode(),
            $state->getLabelData()->getStatus()->getStatusText(),
            $state->getLabelData()->getStatus()->getStatusMessage()
        );

        $data = $state->getLabelData();
        $label = new ResponseData\CreateShipment\Label(
            $labelStatus,
            $state->getSequenceNumber(),
            $data->getLabelData() ? base64_decode($data->getLabelData()) : $data->getLabelData(),
            $data->getReturnLabelData() ? base64_decode($data->getReturnLabelData()) : $data->getReturnLabelData(),
            $data->getExportLabelData() ? base64_decode($data->getExportLabelData()) : $data->getExportLabelData(),
            $data->getCodLabelData() ? base64_decode($data->getCodLabelData()) : $data->getCodLabelData()
        );
        return $label;
    }
}
