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
use \Dhl\Versenden\Webservice\Adapter\Soap as SoapAdapter;
use \Dhl\Versenden\Webservice\Parser\Soap as SoapParser;
use \Dhl\Versenden\Webservice\RequestData;
use \Dhl\Versenden\Webservice\ResponseData;
/**
 * Dhl_Versenden_Model_Webservice_Gateway_Abstract
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class Dhl_Versenden_Model_Webservice_Gateway_Abstract
    implements Dhl_Versenden_Model_Webservice_Gateway
{
    const OPERATION_GET_VERSION = 'getVersion';
    const OPERATION_CREATE_SHIPMENT_ORDER = 'createShipmentOrder';
    const OPERATION_DELETE_SHIPMENT_ORDER = 'deleteShipmentOrder';
    const OPERATION_GET_LABEL = 'getLabel';
    const OPERATION_EXPORT_DOC = 'getExportDoc';
    const OPERATION_DO_MANIFEST = 'doManifest';
    const OPERATION_GET_MANIFEST = 'getManifest';
    const OPERATION_UPDATE_SHIPMENT_ORDER = 'updateShipmentOrder';
    const OPERATION_VALIDATE_SHIPMENT = 'validateShipment';

    /**
     * @param Dhl_Versenden_Model_Config_Shipper $config
     * @return Webservice\Adapter
     */
    abstract public function getAdapter(Dhl_Versenden_Model_Config_Shipper $config);

    /**
     * @param string $operation
     * @return Webservice\Parser
     */
    abstract public function getParser($operation);

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return ResponseData\CreateShipment
     */
    abstract protected function _createShipmentOrder(array $shipmentRequests);

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     */
    protected function _createShipmentOrderBefore(array $shipmentRequests)
    {
        $eventData = array('shipment_requests' => $shipmentRequests);
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_before', $eventData);
    }

    /**
     * @param ResponseData\CreateShipment $result
     */
    protected function _createShipmentOrderAfter(ResponseData\CreateShipment $result)
    {
        $eventData = array('result' => $result);
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_after', $eventData);
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return ResponseData\CreateShipment
     */
    public function createShipmentOrder(array $shipmentRequests)
    {
        $this->_createShipmentOrderBefore($shipmentRequests);
        $result = $this->_createShipmentOrder($shipmentRequests);
        $this->_createShipmentOrderAfter($result);

        return $result;
    }

    /**
     * Create one shipment order from given shipment
     *
     * @param int $sequenceNumber
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param array $packageInfo
     * @param array $serviceInfo
     * @return RequestData\ShipmentOrder
     */
    public function shipmentToShipmentOrder(
        $sequenceNumber,
        Mage_Sales_Model_Order_Shipment $shipment,
        array $packageInfo,
        array $serviceInfo
    ) {
        $helper = Mage::helper('dhl_versenden/webservice');

        // (1) data derived from config
        $shipperConfig = Mage::getModel('dhl_versenden/config_shipper');
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');

        $shipper        = $shipperConfig->getShipper($shipment->getStoreId());
        /** @var RequestData\ShipmentOrder\GlobalSettings $globalSettings */
        $globalSettings = $shipmentConfig->getSettings($shipment->getStoreId());


        // (2) prepared data derived from OPC
        $shippingInfoJson = $shipment->getShippingAddress()->getDhlVersendenInfo();
        $shippingInfoObj = json_decode($shippingInfoJson);
        $shippingInfo = Webservice\RequestData\ObjectMapper::getShippingInfo((object)$shippingInfoObj);
        if (!$shippingInfo) {
            $receiver = $helper->shippingAddressToReceiver($shipment->getShippingAddress());
            $serviceSelection = $helper->serviceSelectionToServiceSettings(
                $serviceInfo['shipment_service'],
                $serviceInfo['service_setting']
            );
        } else {
            $receiver = $shippingInfo->getReceiver();
            $serviceSelection = $shippingInfo->getServiceSelection();
        }

        // (3) data derived from shipment creation
        // add/override shipment and service settings from shipment creation
        $packageCollection = new RequestData\ShipmentOrder\PackageCollection();

        $productCode = '';
        foreach ($packageInfo as $idx => $packageDetails) {
            $weight = $helper->getPackageWeight($globalSettings, $packageDetails['params']['weight']);
            $package = new RequestData\ShipmentOrder\Package($idx, $weight);
            $packageCollection->addItem($package);

            // note: dhl product is the same for all packages
            $productCode = $packageDetails['params']['container'];
        }

        // update shipping info
        $shippingInfo = new Webservice\RequestData\ShippingInfo($receiver, $serviceSelection, $packageCollection);
        $shipment->getShippingAddress()->setDhlVersendenInfo(
            json_encode($shippingInfo, JSON_FORCE_OBJECT)
        );

        return new Webservice\RequestData\ShipmentOrder(
            $sequenceNumber,
            $shipment->getOrder()->getIncrementId(),
            $shipper,
            $receiver,
            $serviceSelection,
            $packageCollection,
            $productCode,
            $helper->utcToCet(null, 'Y-m-d'),
            $globalSettings->isPrintOnlyIfCodable(),
            $globalSettings->getLabelType()
        );
    }
}
