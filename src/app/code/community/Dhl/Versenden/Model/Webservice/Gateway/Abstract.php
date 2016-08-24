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
     * @param RequestData\CreateShipment $requestData
     * @return ResponseData\CreateShipment
     */
    abstract protected function doCreateShipmentOrder(RequestData\CreateShipment $requestData);

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     */
    protected function createShipmentOrderBefore(array $shipmentRequests)
    {
        $eventData = array('shipment_requests' => $shipmentRequests);
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_before', $eventData);
    }

    /**
     * @param ResponseData\CreateShipment $result
     */
    protected function createShipmentOrderAfter(ResponseData\CreateShipment $result)
    {
        $eventData = array('result' => $result);
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_after', $eventData);
    }

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

            try {
                $shipmentOrder = $this->shipmentToShipmentOrder(
                    $sequenceNumber,
                    $orderShipment,
                    $packageInfo,
                    $serviceInfo
                );

                $canShipPartially = ($shipmentOrder->getServiceSelection()->getCod() === null)
                    && ($shipmentOrder->getServiceSelection()->getInsurance() === null);
                $isPartial = ($orderShipment->getOrder()->getTotalQtyOrdered() != $orderShipment->getTotalQty());
                if (!$canShipPartially && $isPartial) {
                    $message = 'Cannot do partial shipment with COD or Additional Insurance.';
                    $message = Mage::helper('dhl_versenden/data')->__($message);
                    throw new RequestData\ValidationException($message);
                }

                $shipmentOrderCollection->addItem($shipmentOrder);
            } catch (RequestData\ValidationException $e) {
                $shipmentRequest->setData('request_data_exception', $e->getMessage());
            }
        }

        return $shipmentOrderCollection;
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return RequestData\CreateShipment
     * @throws RequestData\ValidationException
     */
    protected function prepareCreateShipmentOrderData(array $shipmentRequests)
    {
        $wsVersion = new RequestData\Version('2', '1');
        $shipmentOrders = $this->prepareShipmentOrders($shipmentRequests);

        // handle validation errors in shipment request data
        $shipmentOrderErrors = array();
        foreach ($shipmentRequests as $shipmentRequest) {
            if ($shipmentRequest->hasData('request_data_exception')) {
                $shipmentOrderErrors[]= sprintf(
                    '#%s: %s',
                    $shipmentRequest->getOrderShipment()->getOrder()->getIncrementId(),
                    $shipmentRequest->getData('request_data_exception')
                );
            }
        }

        if (count($shipmentOrderErrors)) {
            $msg = sprintf('%s %s', 'The shipment request(s) had errors.', implode("\n", $shipmentOrderErrors));
            throw new RequestData\ValidationException($msg);
        }

        /** @var RequestData\CreateShipment $requestData */
        return new RequestData\CreateShipment($wsVersion, $shipmentOrders);
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return ResponseData\CreateShipment
     */
    public function createShipmentOrder(array $shipmentRequests)
    {
        $this->createShipmentOrderBefore($shipmentRequests);

        $requestData = $this->prepareCreateShipmentOrderData($shipmentRequests);
        $result = $this->doCreateShipmentOrder($requestData);

        $this->createShipmentOrderAfter($result);

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
        $helper         = Mage::helper('dhl_versenden/data');
        $shipperConfig  = Mage::getModel('dhl_versenden/config_shipper');
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');


        $shipperBuilder = Mage::getModel('dhl_versenden/webservice_builder_shipper', array(
            'config' => $shipperConfig
        ));

        $receiverBuilder = Mage::getModel('dhl_versenden/webservice_builder_receiver', array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => $helper,
        ));

        $serviceBuilder = Mage::getModel('dhl_versenden/webservice_builder_service', array(
            'shipper_config' => $shipperConfig,
            'shipment_config' => $shipmentConfig,
        ));

        $packageBuilder = Mage::getModel('dhl_versenden/webservice_builder_package', array(
            'unit_of_measure' => $shipmentConfig->getSettings($shipment->getStoreId())->getUnitOfMeasure(),
            'min_weight'      => Dhl_Versenden_Model_Shipping_Carrier_Versenden::PACKAGE_MIN_WEIGHT,
        ));

        $settingsBuilder = Mage::getModel('dhl_versenden/webservice_builder_settings', array(
            'config' => $shipmentConfig
        ));

        $orderBuilder = Mage::getModel('dhl_versenden/webservice_builder_order', array(
            'shipper_builder' => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder' => $serviceBuilder,
            'package_builder' => $packageBuilder,
            'settings_builder' => $settingsBuilder,
        ));

        // build shipment order request data
        $shipmentOrder = $orderBuilder->getShipmentOrder(
            $sequenceNumber,
            $helper->utcToCet(null, 'Y-m-d'),
            $shipment,
            $packageInfo,
            $serviceInfo
        );

        // update shipping info
        $shippingInfo = new Webservice\RequestData\ShippingInfo(
            $shipmentOrder->getReceiver(),
            $shipmentOrder->getServiceSelection(),
            $shipmentOrder->getPackages()
        );
        $shipment->getShippingAddress()->setData(
            'dhl_versenden_info',
            json_encode($shippingInfo, JSON_FORCE_OBJECT)
        );

        return $shipmentOrder;
    }
}
