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
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
/**
 * Dhl_Versenden_Model_Shipping_Carrier_Versenden
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Shipping_Autocreate extends Mage_Shipping_Model_Shipping
{
    /**
     * build and return the request for shipment
     *
     * @param Mage_Sales_Model_Order_Shipment $orderShipment
     * @return Mage_Shipping_Model_Shipment_Request
     */
    public function requestToShipment(Mage_Sales_Model_Order_Shipment $orderShipment)
    {
        $admin               = Mage::getSingleton('admin/session')->getUser();
        $order               = $orderShipment->getOrder();
        $address             = $order->getShippingAddress();
        $shippingMethod      = $order->getShippingMethod(true);
        $shipmentStoreId     = $orderShipment->getStoreId();
        $baseCurrencyCode    = Mage::app()->getStore($shipmentStoreId)->getBaseCurrencyCode();
        $recipientRegionCode = Mage::getModel('directory/region')->load($address->getRegionId())->getCode();
        $shipperRegionCode   = $this->getShipperRegionCode($shipmentStoreId);
        $originStreetOne     = Mage::getStoreConfig(self::XML_PATH_STORE_ADDRESS1, $shipmentStoreId);
        $originStreetTwo     = Mage::getStoreConfig(self::XML_PATH_STORE_ADDRESS2, $shipmentStoreId);
        $storeInfo           = new Varien_Object(Mage::getStoreConfig('general/store_information', $shipmentStoreId));
        $shipperCountry      = Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId);
        $recipientCountry    = $address->getCountryId();

        if (!$admin->getFirstname() || !$admin->getLastname() || !$storeInfo->getName() || !$storeInfo->getPhone()
            || !$originStreetOne || !Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId)
            || !$shipperRegionCode || !Mage::getStoreConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId)
            || !Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId)
        ) {
            Mage::throwException(
                Mage::helper('sales')->__(
                    'Insufficient information to create shipping label(s).' .
                    'Please verify your Store Information and Shipping Settings.'
                )
            );
        }

        /** @var $request Mage_Shipping_Model_Shipment_Request */
        $request = Mage::getModel('shipping/shipment_request');
        $request->setOrderShipment($orderShipment);
        $request->setShipperContactPersonName($admin->getName());
        $request->setShipperContactPersonFirstName($admin->getFirstname());
        $request->setShipperContactPersonLastName($admin->getLastname());
        $request->setShipperContactCompanyName($storeInfo->getName());
        $request->setShipperContactPhoneNumber($storeInfo->getPhone());
        $request->setShipperEmail($admin->getEmail());
        $request->setShipperAddressStreet(trim($originStreetOne . ' ' . $originStreetTwo));
        $request->setShipperAddressStreet1($originStreetOne);
        $request->setShipperAddressStreet2($originStreetTwo);
        $request->setShipperAddressCity(Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId));
        $request->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $request->setShipperAddressPostalCode(Mage::getStoreConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId));
        $request->setShipperAddressCountryCode($shipperCountry);
        $request->setRecipientContactPersonName(trim($address->getFirstname() . ' ' . $address->getLastname()));
        $request->setRecipientContactPersonFirstName($address->getFirstname());
        $request->setRecipientContactPersonLastName($address->getLastname());
        $request->setRecipientContactCompanyName($address->getCompany());
        $request->setRecipientContactPhoneNumber($address->getTelephone());
        $request->setRecipientEmail($address->getEmail());
        $request->setRecipientAddressStreet(trim($address->getStreet1() . ' ' . $address->getStreet2()));
        $request->setRecipientAddressStreet1($address->getStreet1());
        $request->setRecipientAddressStreet2($address->getStreet2());
        $request->setRecipientAddressCity($address->getCity());
        $request->setRecipientAddressStateOrProvinceCode($address->getRegionCode());
        $request->setRecipientAddressRegionCode($recipientRegionCode);
        $request->setRecipientAddressPostalCode($address->getPostcode());
        $request->setRecipientAddressCountryCode($recipientCountry);
        $request->setShippingMethod($shippingMethod->getMethod());
        $request->setPackageWeight($order->getWeight());
        $request->setPackages($orderShipment->getPackages());
        $request->setBaseCurrencyCode($baseCurrencyCode);
        $request->setStoreId($shipmentStoreId);

        // add service data to request object
        $request->setData('services', $this->getConfiguredServices());
        // add dhl versenden product to request object
        $request->setData('gk_api_product', $this->getDhlVersendenProducts($shipperCountry, $recipientCountry));


        return $request;
    }

    /**
     * create shipments
     *
     *
     * @return $this
     */
    public function autoCreateShippment()
    {
        $shipmentRequests = array();
        $orderCollection  = Mage::helper('dhl_versenden/data')->getOrdersForAutoCreateShippment();

        if ($orderCollection->count() == 0) {
            return;
        }
        /** @var Mage_Sales_Model_Order $order */
        foreach ($orderCollection as $order) {
            try {
                $shipment = $order->prepareShipment();
                $shipment->register();
                $shipment->setPackages($this->getPackagesForShipment($order));
                $shipmentRequests[$order->getId()] = $this->requestToShipment($shipment);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $soapWebservice */
        $soapWebservice = Mage::getModel('dhl_versenden/webservice_gateway_soap');
        $result         = $soapWebservice->createShipmentOrder($shipmentRequests);

        $transaction = Mage::getModel('core/resource_transaction');
        $pdfLib      = new \Dhl\Versenden\Pdf\Adapter\Zend();
        /** @var Mage_Shipping_Model_Shipment_Request $shipmentRequest */
        foreach ($shipmentRequests as $orderId => $shipmentRequest) {
            $shipment = $shipmentRequest->getOrderShipment();
            $shipmentNumber = $result->getShipmentNumber($orderId);
            $shipmentStatus = $result->getLabels()->getItem($shipmentNumber)->getStatus();
            if ($shipmentStatus->isError()) {
                Mage::helper('dhl_versenden/data')->addStatusHistoryError(
                    $order,
                    $shipmentStatus->getStatusText() . implode(':', $shipmentStatus->getStatusMessage()),
                    Zend_Log::ERR
                );
            } else {
                $labels = $result->getLabels()->getItem($shipmentNumber)->getAllLabels($pdfLib);
                $shipment->setShippingLabel($labels);
                $carrierTitle = 'DHL Versenden Autocreate';
                $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($shipmentNumber)
                    ->setCarrierCode($shipment->getOrder()->getShippingMethod())
                    ->setTitle($carrierTitle);
                $shipment->addTrack($track);
                $order->setIsInProcess(true);
                $transaction
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder());
            }
        }

        $transaction->save();

        return $this;
    }

    /**
     * Obtain packages for shipment
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function getPackagesForShipment(Mage_Sales_Model_Order $order)
    {
        $packageData = array();
        $weight      = 0;

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            if (!$item->canShip()) {
                continue;
            }
            $weight +=  (float) $item->getWeight() * (float) $item->getQtyOrdered();
        }

        return $packageData['params'][] = array('weight' => $weight);
    }

    /**
     * Obtain shipper region code
     *
     * @param $shipmentStoreId
     * @return mixed
     */
    protected function getShipperRegionCode($shipmentStoreId)
    {
        $shipperRegionCode = Mage::getStoreConfig(self::XML_PATH_STORE_REGION_ID, $shipmentStoreId);
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = Mage::getModel('directory/region')->load($shipperRegionCode)->getCode();
            return $shipperRegionCode;
        }
        return $shipperRegionCode;
    }

    /**
     * get service data from config
     *
     * @return array
     */
    protected function getConfiguredServices()
    {
        $services = Mage::getModel('dhl_versenden/config')->getAutoCreateServices();
        $serviceData = array(
            'shipment_service' => array(),
            'service_setting'  => array(),
        );

        foreach ($services as $name => $value) {
            $serviceData['shipment_service'][$name] = (bool) $value;
            $serviceData['service_setting'][$name]  = $value;
        }

        return $serviceData;
    }

    /**
     * get dhl versenden products based on shipper and recipient country
     *
     * @param $shipperCountry
     * @param $recipientCountry
     * @return mixed
     */
    protected function getDhlVersendenProducts($shipperCountry, $recipientCountry)
    {
        $products     = Mage::getModel('dhl_versenden/shipping_carrier_versenden')
            ->getProducts($shipperCountry, $recipientCountry);
        $productCodes = array_keys($products);

        return $productCodes[0];
    }
}
