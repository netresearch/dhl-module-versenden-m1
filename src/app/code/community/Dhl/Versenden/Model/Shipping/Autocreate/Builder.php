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
use \Dhl\Versenden\Bcs\Api\Shipment\Service;
/**
 * Dhl_Versenden_Model_Shipping_Autocreate_Builder
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Shipping_Autocreate_Builder
{
    /** @var Mage_Sales_Model_Order */
    protected $_order;
    /** @var Dhl_Versenden_Model_Config_Shipment */
    protected $_shipmentConfig;
    /** @var Dhl_Versenden_Model_Config_Shipper */
    protected $_shipperConfig;
    /** @var Dhl_Versenden_Model_Config_Service */
    protected $_serviceConfig;

    /**
     * Dhl_Versenden_Model_Shipping_Autocreate_Builder constructor.
     *
     * @param Mage_Sales_Model_Order $order
     * @param Dhl_Versenden_Model_Config_Shipment $shipmentConfig
     * @param Dhl_Versenden_Model_Config_Shipper $shipperConfig
     * @param Dhl_Versenden_Model_Config_Service $serviceConfig
     */
    public function __construct(
        Mage_Sales_Model_Order $order,
        Dhl_Versenden_Model_Config_Shipment $shipmentConfig,
        Dhl_Versenden_Model_Config_Shipper $shipperConfig,
        Dhl_Versenden_Model_Config_Service $serviceConfig
    ) {
        $this->_order = $order;
        $this->_shipmentConfig = $shipmentConfig;
        $this->_shipperConfig = $shipperConfig;
        $this->_serviceConfig = $serviceConfig;
    }

    /**
     * Create shipment request for given shipment.
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return Mage_Shipping_Model_Shipment_Request
     */
    public function createShipmentRequest(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $request = Mage::getModel('shipping/shipment_request');

        $request->setOrderShipment($shipment);
        $request->setShippingMethod($this->_order->getShippingMethod());
        $request->setPackageWeight($this->_order->getWeight());
        $request->setBaseCurrencyCode(Mage::app()->getStore($shipment->getStoreId())->getBaseCurrencyCode());
        $request->setStoreId($shipment->getStoreId());

        $this->setShipper($request);
        $this->setRecipient($request);
        $this->setPackages($request);
        $this->setServices($request);
        $this->setGkApiProduct($request);
        $this->setCustomsInfo($request);

        return $request;
    }

    /**
     * Add shipping origin info to request.
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setShipper(Mage_Shipping_Model_Shipment_Request $request)
    {
        /** @var \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Shipper\Contact $contact */
        $contact = $this->_shipperConfig->getContact($this->_order->getStoreId());
        $request->setShipperContactPersonName($contact->getName1());
        $request->setShipperContactCompanyName($contact->getName2());
        $request->setShipperContactPhoneNumber($contact->getPhone());
        $request->setShipperEmail($contact->getEmail());
        $request->setShipperAddressStreet(trim($contact->getStreetName() . ' ' . $contact->getStreetNumber()));
        $request->setShipperAddressStreet1($contact->getStreetName());
        $request->setShipperAddressStreet2($contact->getStreetNumber());
        $request->setShipperAddressCity($contact->getCity());
        $request->setShipperAddressPostalCode($contact->getZip());
        $request->setShipperAddressCountryCode($contact->getCountryISOCode());
    }

    /**
     * Add shipment address info to request.
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setRecipient(Mage_Shipping_Model_Shipment_Request $request)
    {
        $address             = $this->_order->getShippingAddress();
        $recipientRegionCode = Mage::getModel('directory/region')->load($address->getRegionId())->getCode();

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
        $request->setRecipientAddressCountryCode($address->getCountryId());
    }

    /**
     * Add package details to request.
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setPackages(Mage_Shipping_Model_Shipment_Request $request)
    {
        $packageItems = array();
        $totalWeight = 0;
        $totalCustomsValue = 0;

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($this->_order->getAllItems() as $item) {
            if (!$item->isDummy($request->getOrderShipment())) {
                $packageItem = array();
                $packageItem['qty'] = $item->getQtyShipped();
                $packageItem['customs_value'] = $item->getBasePrice();
                $packageItem['price'] = $item->getPrice();
                $packageItem['name'] = $item->getName();
                $packageItem['weight'] = $item->getWeight();
                $packageItem['product_id'] = $item->getProductId();
                $packageItem['order_item_id'] = $item->getId();

                $packageItems[$item->getId()] = $packageItem;

                $totalWeight+= ($item->getQtyShipped() * $item->getWeight());
                $totalCustomsValue+= ($item->getQtyShipped() * $item->getBasePrice());
            }
        }

        $packageParams = array(
            'weight' => $totalWeight,
            'customs_value' => $totalCustomsValue,
            'weight_units' => $this->_shipmentConfig->getSettings($this->_order->getStoreId())->getUnitOfMeasure(),
        );

        $packageData = array(
            // package_1:
            '1' => array(
                'params' => $packageParams,
                'items' => $packageItems
            ),
        );

        $request->getOrderShipment()->setData('packages', $packageData);
        $request->setData('packages', $packageData);
    }

    /**
     * Add merchant and customer services to request.
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setServices(Mage_Shipping_Model_Shipment_Request $request)
    {
        $storeId = $this->_order->getStoreId();

        // set merchant services from autocreate config
        $services = $this->_serviceConfig->getAutoCreateServices($storeId);
        $shippingAddress = $this->_order->getShippingAddress();
        $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $storeId));

        $shippingProducts = \Dhl\Versenden\Bcs\Api\Product::getCodesByCountry(
            $shipperCountry, $recipientCountry, $euCountries
        );
        $isPostalFacility = Mage::helper('dhl_versenden/data')->isPostalFacility($shippingAddress);

        $serviceFilter = new \Dhl\Versenden\Bcs\Api\Shipment\Service\Filter(
            $shippingProducts, $isPostalFacility, false
        );
        $filteredServiceCollection = $serviceFilter->filterServiceCollection($services);

        $serviceData = array(
            'shipment_service' => array(),
            'service_setting'  => array(),
        );

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic $service */
        foreach ($filteredServiceCollection as $service) {
            $serviceData['shipment_service'][$service->getCode()] = $service->isEnabled();
            $serviceData['service_setting'][$service->getCode()] = $service->getValue();
        }


        // add printOnlyIfCodeable flag from config
        $serviceData['shipment_service'][Service\PrintOnlyIfCodeable::CODE] =
            $this->_shipmentConfig->getSettings($storeId)->isPrintOnlyIfCodeable();
        // add parcelAnnouncement flag from config
        $parcelAnnouncement = $this->_serviceConfig->getEnabledServices($storeId)
                ->getItem(Service\ParcelAnnouncement::CODE);
        if (($parcelAnnouncement instanceof Service\ParcelAnnouncement) && !$parcelAnnouncement->isCustomerService()) {
            $serviceData['shipment_service'][Service\ParcelAnnouncement::CODE] = true;
        }

        // set customer services from checkout (includes parcelAnnouncement if configured as "optional")
        /** @var \Dhl\Versenden\Bcs\Api\Info $versendenInfo */
        $versendenInfo = $this->_order->getShippingAddress()->getData('dhl_versenden_info');
        if ($versendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            $customerServices = $this->_serviceConfig->getAvailableServices(
                $shipperCountry,
                $recipientCountry,
                $isPostalFacility,
                true,
                $storeId
            );
            /** @var Service\Type\Generic $customerService */
            foreach ($customerServices as $customerService) {
                $code = $customerService->getCode();
                $serviceData['shipment_service'][$code] = (bool)$versendenInfo->getServices()->{$code};
                $serviceData['service_setting'][$code] = (string)$versendenInfo->getServices()->{$code};
            }
        }

        $request->setData('services', $serviceData);
    }

    /**
     * Determine the product to be used with the current shipment.
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setGkApiProduct(Mage_Shipping_Model_Shipment_Request $request)
    {
        $shipperCountry = $this->_shipperConfig->getContact($this->_order->getStoreId())->getCountryISOCode();
        $recipientCountry = $this->_order->getShippingAddress()->getCountryId();

        $products = Mage::getModel('dhl_versenden/shipping_carrier_versenden')
            ->getProducts($shipperCountry, $recipientCountry);
        $productCodes = array_keys($products);

        $request->setData('gk_api_product', $productCodes[0]);
    }

    /**
     * Add customs info to request: empty, international shipments are note supported.
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setCustomsInfo(Mage_Shipping_Model_Shipment_Request $request)
    {
        $request->setData('customs', array());
    }
}
