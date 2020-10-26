<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Shipper;

class Dhl_Versenden_Model_Webservice_Builder_Shipper
{
    /** @var Dhl_Versenden_Model_Config_Shipper */
    protected $config;

    /** @var Mage_Sales_Model_Order_Shipment */
    protected $shipment;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Shipper constructor.
     * @param Dhl_Versenden_Model_Config[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }
        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipper) {
            Mage::throwException("invalid argument: $argName");
        }
        $this->config = $args[$argName];
    }

    /**
     * @param mixed $store
     * @return Shipper
     */
    public function getShipper($store)
    {
        $shipper = $this->config->getShipper($store);

        if ($this->shipment != null)
        {
            $shipper = new Shipper(
                $this->config->getAccountSettings(),
                $this->config->getBankData($store, $this->getBankRefMap()),
                $this->config->getContact($store),
                $this->config->getReturnReceiver($store)
            );
        }
        return $shipper;
    }

     /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return Shipment
     */
    public function setShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $this->shipment = $shipment;
        return $this;
    }

    /**
     * @return array
     */
    protected function getBankRefMap()
    {
        // Available placeholders for the bank data configuration
        return array(
            '%orderId%'      => $this->shipment->getOrder()->getIncrementId(),
            '%customerId%'   => $this->shipment->getOrder()->getCustomerId()
        );
    }
}
