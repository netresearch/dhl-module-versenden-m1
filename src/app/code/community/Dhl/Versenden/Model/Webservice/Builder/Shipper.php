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
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Shipper;
/**
 * Dhl_Versenden_Model_Webservice_Builder_Shipper
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
