<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

interface Dhl_Versenden_Model_Webservice_Gateway
{
    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return ResponseData\CreateShipment
     */
    public function createShipmentOrder(array $shipmentRequests);

    /**
     * @param string[] $shipmentNumbers
     * @return ResponseData\DeleteShipment
     */
    public function deleteShipmentOrder(array $shipmentNumbers);
}
