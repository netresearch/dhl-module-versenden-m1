<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap;
use \Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice;

class DeleteShipmentOrder extends Shipment implements Webservice\Parser
{
    /**
     * @param VersendenApi\DeleteShipmentOrderResponse $response
     * @return Webservice\ResponseData\DeleteShipment
     */
    public function parse($response)
    {
        $status = $this->parseResponseStatus($response->getStatus());

        // with the SoapClient SOAP_SINGLE_ELEMENT_ARRAYS feature enabled, $deletionStates is always an array
        $deletionStates = $response->getDeletionState();

        $deletedItems = new Webservice\ResponseData\DeleteShipment\StatusCollection();

        /** @var VersendenApi\DeletionState $deletionState */
        foreach ($deletionStates as $deletionState) {
            $deletedItem = $this->parseItemStatus($deletionState->getShipmentNumber(), $deletionState->getStatus());
            $deletedItems->addItem($deletedItem);
        }

        return new Webservice\ResponseData\DeleteShipment($status, $deletedItems);
    }
}

