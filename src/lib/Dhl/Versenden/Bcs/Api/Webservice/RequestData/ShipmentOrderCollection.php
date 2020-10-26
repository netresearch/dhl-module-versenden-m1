<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class ShipmentOrderCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var ShipmentOrder[]
     */
    protected $shipmentOrders = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->shipmentOrders);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->shipmentOrders);
    }

    /**
     * Set all shipment orders to the collection.
     *
     * @param ShipmentOrder[] $shipmentOrders
     * @return $this
     */
    public function setItems(array $shipmentOrders)
    {
        $this->shipmentOrders = [];
        foreach ($shipmentOrders as $shipmentOrder) {
            $this->addItem($shipmentOrder);
        }

        return $this;
    }

    /**
     * Obtain all shipment orders from collection
     *
     * @return ShipmentOrder[]
     */
    public function getItems()
    {
        return $this->shipmentOrders;
    }

    /**
     * Add a shipment order to the collection.
     *
     * @param ShipmentOrder $shipmentOrder
     * @return $this
     */
    public function addItem(ShipmentOrder $shipmentOrder)
    {
        $this->shipmentOrders[$shipmentOrder->getSequenceNumber()] = $shipmentOrder;

        return $this;
    }

    /**
     * @param $sequenceNumber
     * @return ShipmentOrder|null
     */
    public function getItem($sequenceNumber)
    {
        if (!isset($this->shipmentOrders[$sequenceNumber])) {
            return null;
        }

        return $this->shipmentOrders[$sequenceNumber];
    }
}
