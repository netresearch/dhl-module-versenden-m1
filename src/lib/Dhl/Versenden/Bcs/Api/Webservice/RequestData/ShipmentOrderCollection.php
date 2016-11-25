<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData;
/**
 * ShipmentOrderCollection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
