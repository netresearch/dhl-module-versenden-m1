<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class CreateShipment extends RequestData
{
    /** @var Version */
    private $version;
    /** @var ShipmentOrder[] */
    private $shipmentOrders;

    /**
     * CreateShipment constructor.
     * @param Version $version
     * @param ShipmentOrderCollection $shipmentOrders
     */
    public function __construct(Version $version, ShipmentOrderCollection $shipmentOrders)
    {
        $this->version = $version;
        $this->shipmentOrders = $shipmentOrders;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return ShipmentOrderCollection
     */
    public function getShipmentOrders()
    {
        return $this->shipmentOrders;
    }
}
