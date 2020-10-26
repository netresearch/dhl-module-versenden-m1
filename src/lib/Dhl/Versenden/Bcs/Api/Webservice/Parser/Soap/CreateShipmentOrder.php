<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap;

use \Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice;

class CreateShipmentOrder extends ShipmentLabel implements Webservice\Parser
{
    /**
     * @param VersendenApi\CreateShipmentOrderResponse $response
     * @return Webservice\ResponseData\CreateShipment
     * @throws Webservice\ResponseData\Status\Exception
     */
    public function parse($response)
    {
        $status = $this->parseResponseStatus($response->getStatus());

        if (in_array($status->getStatusCode(), array(112, 118, 1001), true)) {
            // authentication failure
            throw new Webservice\ResponseData\Status\Exception($status);
        }

        // with the SoapClient SOAP_SINGLE_ELEMENT_ARRAYS feature enabled, $creationStates is always an array
        $creationStates = $response->getCreationState();

        $sequence = array();
        $labels = new Webservice\ResponseData\CreateShipment\LabelCollection();

        /** @var VersendenApi\CreationState $creationState */
        foreach ($creationStates as $creationState) {
            $sequence[$creationState->getSequenceNumber()] = $creationState->getShipmentNumber();
            $label = $this->parseLabel($creationState);
            $labels->addItem($label);
        }

        return new Webservice\ResponseData\CreateShipment($status, $labels, $sequence);
    }
}
