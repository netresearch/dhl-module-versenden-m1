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
use \Dhl\Versenden\Webservice;
use \Dhl\Bcs\Api as VersendenApi;
/**
 * Dhl_Versenden_Helper_Webservice
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Helper_Webservice extends Mage_Core_Helper_Abstract
{
    const ADAPTER_TYPE_SOAP = 'soap';

    /**
     * @return Webservice\Adapter\Soap
     */
    protected function getSoapAdapter()
    {
        $config = Mage::getModel('dhl_versenden/config_shipper');

        $options = array(
            'location' => $config->getEndpoint(),
            'login' => $config->getWebserviceAuthUsername(),
            'password' => $config->getWebserviceAuthPassword(),
        );
        $client = new \Dhl\Bcs\Api\GVAPI_2_0_de($options);

        $authHeader = new \SoapHeader(
            'http://dhl.de/webservice/cisbase',
            'Authentification',
            array(
                'user' => $config->getAccountSettings()->getUser(),
                'signature' => $config->getAccountSettings()->getSignature(),
            )
        );
        $client->__setSoapHeaders($authHeader);

        $adapter = new Webservice\Adapter\Soap($client);

        return $adapter;
    }

    /**
     * Convert a timestamp to a CE(S)T time string.
     *
     * @param string $timestamp The timestamp to convert
     * @param string $format The output format
     * @return string
     */
    public function utcToCet($timestamp = null, $format = 'Y-m-d H:i:s')
    {
        if (null === $timestamp) {
            $timestamp = time();
        }

        $date = new DateTime("@$timestamp");
        $timezoneCet = new DateTimeZone('Europe/Berlin');

        $intervalSpec = sprintf("PT%dS", $timezoneCet->getOffset($date));
        $date->add(new DateInterval($intervalSpec));

        return $date->format($format);
    }

    /**
     * @param string $type
     * @return Webservice\Adapter
     */
    public function getWebserviceAdapter($type)
    {
        $adapter = null;

        switch ($type) {
            case self::ADAPTER_TYPE_SOAP:
            default:
                $adapter = $this->getSoapAdapter();
        }

        return $adapter;
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment_Item[] $shipmentItems
     * @param int $defaultWeight
     * @param string $unit
     * @return float
     */
    public function calculateItemsWeight($shipmentItems = array(), $defaultWeight = 200, $unit = 'G')
    {
        $sumWeight = function ($totalWeight, Mage_Sales_Model_Order_Shipment_Item $item) use ($defaultWeight) {
            $totalWeight += $item->getWeight() ? $item->getWeight() : $defaultWeight;
            return $totalWeight;
        };
        $totalWeight = array_reduce($shipmentItems, $sumWeight, 0);

        if ($unit === 'G') {
            $totalWeight *= 1000;
        }
        return $totalWeight;
    }

    /**
     * Create one shipment order from the given data
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param int $sequenceNumber
     * @param array $orderData
     * @return Webservice\RequestData\ShipmentOrder
     */
    public function shipmentToOrder(
        Mage_Sales_Model_Order_Shipment $shipment, $sequenceNumber = 1, $orderData = array()
    ) {
        $shipperConfig = Mage::getModel('dhl_versenden/config_shipper');
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');

        // (1) data derived from config
        $shipper        = $shipperConfig->getShipper($shipment->getStoreId());
        /** @var Webservice\RequestData\ShipmentOrder\GlobalSettings $globalSettings */
        $globalSettings = $shipmentConfig->getSettings($shipment->getStoreId());

        // (2) data derived from OPC
        $shippingInfo = new \Dhl\Versenden\ShippingInfo();
        $shippingInfo->setJson($shipment->getShippingAddress()->getDhlVersendenInfo());

        // (2.1) shipping address
        $receiver = $shippingInfo->shippingAddress;

        // (2.2) service selection
        //TODO(nr): add/override service settings using $orderData
        $serviceSettings = $shippingInfo->serviceSettings;


        // (3) data derived from shipment creation
        // add/override shipment settings from shipment creation
        $shipmentSettings = $shippingInfo->shipmentSettings;
        $shipmentSettings->date = $this->utcToCet(null, 'Y-m-d');
        $shipmentSettings->reference = $shipment->getOrder()->getIncrementId();
        $shipmentSettings->weight = $this->calculateItemsWeight(
            $shipment->getAllItems(),
            $globalSettings->getProductWeight(),
            $globalSettings->getUnitOfMeasure()
        );
        $shipmentSettings->weight = $this->calculateItemsWeight($globalSettings, $shipment->getAllItems());
        $shipmentSettings->dhlProduct = $orderData['product'];

        return new Webservice\RequestData\ShipmentOrder(
            $globalSettings,
            $shipper,
            $receiver,
            $shipmentSettings,
            $serviceSettings,
            $sequenceNumber,
            $globalSettings->getLabelType()
        );
    }
}
