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

/**
 * Dhl_Versenden_Model_Shipping_Autocreate_Request
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
    protected $order;
    /** @var Dhl_Versenden_Model_Config_Shipper */
    protected $shipperConfig;
    /** @var Dhl_Versenden_Model_Config_Service */
    protected $serviceConfig;

    public function __construct(Mage_Sales_Model_Order $order,
                                Dhl_Versenden_Model_Config_Shipper $shipperConfig,
                                Dhl_Versenden_Model_Config_Service $serviceConfig
    )
    {
        $this->order = $order;
        $this->shipperConfig = $shipperConfig;
        $this->serviceConfig = $serviceConfig;
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
        $request->setShippingMethod($this->order->getShippingMethod());
        $request->setPackageWeight($this->order->getWeight());
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
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setShipper(Mage_Shipping_Model_Shipment_Request $request)
    {
        /** @var \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\Contact $contact */
        $contact = $this->shipperConfig->getContact($this->order->getStoreId());
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
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setRecipient(Mage_Shipping_Model_Shipment_Request $request)
    {
        $address             = $this->order->getShippingAddress();
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
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setPackages(Mage_Shipping_Model_Shipment_Request $request)
    {
        $weight = array_reduce(
            $this->order->getAllItems(),
            function ($carry, Mage_Sales_Model_Order_Item $item) {
                if ($item->canShip()) {
                    $carry += (float) $item->getWeight() * (float) $item->getQtyOrdered();
                }
                return $carry;
            },
            0
        );

        $packageData = array(array('params' => array('weight' => $weight)));
        $request->setData('packages', $packageData);
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setServices(Mage_Shipping_Model_Shipment_Request $request)
    {
        $services = $this->serviceConfig->getAutoCreateServices($this->order->getStoreId());

        $serviceData = array(
            'shipment_service' => array(),
            'service_setting'  => array(),
        );

        /** @var \Dhl\Versenden\Shipment\Service\Type\Generic $service */
        foreach ($services as $service) {
            $serviceData['shipment_service'][$service->getCode()] = $service->isEnabled();
            $serviceData['service_setting'][$service->getCode()] = $service->getValue();
        }

        $request->setData('services', $serviceData);
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setGkApiProduct(Mage_Shipping_Model_Shipment_Request $request)
    {
        $shipperCountry = $this->shipperConfig->getContact($this->order->getStoreId())->getCountryISOCode();
        $recipientCountry = $this->order->getShippingAddress()->getCountryId();

        $products = Mage::getModel('dhl_versenden/shipping_carrier_versenden')
            ->getProducts($shipperCountry, $recipientCountry);
        $productCodes = array_keys($products);

        $request->setData('gk_api_product', $productCodes[0]);
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    protected function setCustomsInfo(Mage_Shipping_Model_Shipment_Request $request)
    {
        $request->setData('customs', array());
    }
}
