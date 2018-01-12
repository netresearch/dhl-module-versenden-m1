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
use \Dhl\Versenden\Bcs\Api\Webservice;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData;
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;
/**
 * Dhl_Versenden_Model_Webservice_Gateway_Abstract
 *
 * Note: adapter, parser and logger should get injected during object instantiation
 * but we do not have proper constructors in M1's alias factory.
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

    const WEBSERVICE_VERSION_MAJOR = '2';
    const WEBSERVICE_VERSION_MINOR = '2';
    const WEBSERVICE_VERSION_BUILD = '';

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
     * @param Dhl_Versenden_Model_Config $config
     * @param Dhl_Versenden_Model_Logger_Writer $writer
     * @return Dhl_Versenden_Model_Webservice_Logger_Interface
     */
    abstract public function getLogger(Dhl_Versenden_Model_Config $config, Dhl_Versenden_Model_Logger_Writer $writer);

    /**
     * @param RequestData\CreateShipment $requestData
     * @return ResponseData\CreateShipment
     */
    abstract protected function doCreateShipmentOrder(RequestData\CreateShipment $requestData);

    /**
     * @param RequestData\DeleteShipment $requestData
     * @return ResponseData\DeleteShipment
     */
    abstract protected function doDeleteShipmentOrder(RequestData\DeleteShipment $requestData);

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return RequestData\ShipmentOrderCollection
     */
    protected function prepareShipmentOrders(array $shipmentRequests)
    {
        $shipmentOrderCollection = new RequestData\ShipmentOrderCollection();

        /** @var Mage_Shipping_Model_Shipment_Request $shipmentRequest */
        foreach ($shipmentRequests as $sequenceNumber => $shipmentRequest) {
            $orderShipment = $shipmentRequest->getOrderShipment();

            $packageInfo = $shipmentRequest->getData('packages');
            $serviceInfo = $shipmentRequest->getData('services');
            $customsInfo = $shipmentRequest->getData('customs');

            try {
                $shipmentOrder = $this->shipmentToShipmentOrder(
                    $sequenceNumber,
                    $orderShipment,
                    $packageInfo,
                    $serviceInfo,
                    $customsInfo,
                    $shipmentRequest->getData('gk_api_product')
                );

                $cod = $shipmentOrder->getServiceSelection()->getCod();
                $insurance = $shipmentOrder->getServiceSelection()->getInsurance();
                $canShipPartially = empty($cod)
                    && empty($insurance);
                $isPartial = ($orderShipment->getOrder()->getTotalQtyOrdered() != $orderShipment->getTotalQty());
                if (!$canShipPartially && $isPartial) {
                    $message = 'Cannot do partial shipment with COD or Additional Insurance.';
                    throw new RequestData\ValidationException($message);
                }

                $shipmentOrderCollection->addItem($shipmentOrder);
            } catch (RequestData\ValidationException $e) {
                $shipmentRequest->setData(
                    'request_data_exception',
                    Mage::helper('dhl_versenden/data')->__($e->getMessage())
                );
            }
        }

        return $shipmentOrderCollection;
    }

    /**
     * Prepare request data and pass on to concrete gateway.
     *
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return ResponseData\CreateShipment|null
     */
    public function createShipmentOrder(array $shipmentRequests)
    {
        $eventData = array('shipment_requests' => $shipmentRequests);
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_before', $eventData);

        $wsVersion = new RequestData\Version(self::WEBSERVICE_VERSION_MAJOR, self::WEBSERVICE_VERSION_MINOR);
        $shipmentOrders = $this->prepareShipmentOrders($shipmentRequests);
        $items = $shipmentOrders->getItems();

        if (!empty($items)) {
            /** @var RequestData\CreateShipment $requestData */
            $requestData = new RequestData\CreateShipment($wsVersion, $shipmentOrders);
            $result = $this->doCreateShipmentOrder($requestData);
        } else {
            $result = null;
        }

        $eventData = array();
        $eventData['request_data'] = $shipmentRequests;
        $eventData['result'] = $result;
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_after', $eventData);

        return $result;
    }

    /**
     * @param string[] $shipmentNumbers
     * @return ResponseData\DeleteShipment
     */
    public function deleteShipmentOrder(array $shipmentNumbers)
    {
        $eventData = array('shipment_numbers' => $shipmentNumbers);
        Mage::dispatchEvent('dhl_versenden_delete_shipment_order_before', $eventData);

        $wsVersion = new RequestData\Version(self::WEBSERVICE_VERSION_MAJOR, self::WEBSERVICE_VERSION_MINOR);
        $requestData = new RequestData\DeleteShipment($wsVersion, $shipmentNumbers);
        $result = $this->doDeleteShipmentOrder($requestData);

        $eventData = array();
        $eventData['request_data'] = $shipmentNumbers;
        $eventData['result'] = $result;
        Mage::dispatchEvent('dhl_versenden_delete_shipment_order_after', $eventData);

        return $result;
    }

    /**
     * Create one shipment order from given shipment
     *
     * @param int $sequenceNumber
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param string[] $packageInfo
     * @param string[] $serviceInfo
     * @param string[] $customsInfo
     * @param string $gkApiProduct
     * @return RequestData\ShipmentOrder
     */
    public function shipmentToShipmentOrder(
        $sequenceNumber,
        Mage_Sales_Model_Order_Shipment $shipment,
        array $packageInfo,
        array $serviceInfo,
        array $customsInfo,
        $gkApiProduct
    ) {
        $shipperConfig  = Mage::getModel('dhl_versenden/config_shipper');
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');


        $args = array('config' => $shipperConfig);
        $shipperBuilder = Mage::getModel('dhl_versenden/webservice_builder_shipper', $args);

        $args = array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper'            => Mage::helper('dhl_versenden/address'),
        );
        $receiverBuilder = Mage::getModel('dhl_versenden/webservice_builder_receiver', $args);

        $args = array(
            'shipper_config'  => $shipperConfig,
            'shipment_config' => $shipmentConfig,
        );
        $serviceBuilder = Mage::getModel('dhl_versenden/webservice_builder_service', $args);

        $args = array(
            'unit_of_measure' => $shipmentConfig->getSettings($shipment->getStoreId())->getUnitOfMeasure(),
            'min_weight'      => Dhl_Versenden_Model_Shipping_Carrier_Versenden::PACKAGE_MIN_WEIGHT,
        );
        $packageBuilder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $customsBuilder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);

        $args = array('config' => $shipmentConfig);
        $settingsBuilder = Mage::getModel('dhl_versenden/webservice_builder_settings', $args);

        $infoBuilder = Mage::getModel('dhl_versenden/info_builder');

        $args = array(
            'shipper_builder'  => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder'  => $serviceBuilder,
            'package_builder'  => $packageBuilder,
            'customs_builder'  => $customsBuilder,
            'settings_builder' => $settingsBuilder,
            'info_builder'     => $infoBuilder,
        );
        $orderBuilder = Mage::getModel('dhl_versenden/webservice_builder_order', $args);

        // build shipment order request data
        $shipmentOrder = $orderBuilder->getShipmentOrder(
            $sequenceNumber,
            Mage::helper('dhl_versenden/data')->utcToCet(null, 'Y-m-d'),
            $shipment,
            $packageInfo,
            $serviceInfo,
            $customsInfo,
            $gkApiProduct
        );

        return $shipmentOrder;
    }
}
