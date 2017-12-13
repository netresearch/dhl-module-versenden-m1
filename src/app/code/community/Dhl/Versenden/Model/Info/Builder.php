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
use \Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
/**
 * Dhl_Versenden_Model_Info_Builder
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Info_Builder
{
    /**
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $shippingAddress
     * @param mixed[] $serviceInfo
     * @param mixed $store
     * @return \Dhl\Versenden\Bcs\Api\Info
     */
    public function infoFromSales(
        Mage_Customer_Model_Address_Abstract $shippingAddress,
        array $serviceInfo,
        $store = null
    ) {
        $versendenInfo = new \Dhl\Versenden\Bcs\Api\Info();
        $serviceConfig  = Mage::getModel('dhl_versenden/config_service');

        $selectedServices = $serviceInfo['shipment_service'];
        $selectedSettings = $serviceInfo['service_setting'];

        $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($store);
        $recipientCountry = $shippingAddress->getCountryId();
        $isPostalFacility = Mage::helper('dhl_versenden/data')->isPostalFacility($shippingAddress);
        $availableServices = $serviceConfig->getAvailableServices(
            $shipperCountry,
            $recipientCountry,
            $isPostalFacility,
            true,
            $store
        );

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic $availableService */
        foreach ($availableServices as $availableService) {
            $code = $availableService->getCode();
            if (isset($selectedServices[$code])) {
                $details = isset($selectedSettings[$code]) ? $selectedSettings[$code] : (bool)$selectedServices[$code];
                $serviceInfo[$code] = $details;
            }
        }

        $versendenInfo->getServices()->fromArray($serviceInfo, false);

        // set processed recipient address
        $countryDirectory = Mage::getModel('directory/country')->loadByCode($shippingAddress->getCountryId());
        $street = $shippingAddress->getStreetFull();
        $street = Mage::helper('dhl_versenden/address')->splitStreet($street);
        // let 3rd party extensions add postal facility data
        $facility = new Varien_Object();
        Mage::dispatchEvent(
            'dhl_versenden_fetch_postal_facility', array(
                'customer_address'   => $shippingAddress,
                'postal_facility' => $facility,
            )
        );

        $packstationInfo = array();
        if ($facility->getData('shop_type') === PostalFacility::TYPE_PACKSTATION) {
            $packstationInfo = array(
                'zip' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity(),
                'country' => $countryDirectory->getName(),
                'countryISOCode' => $countryDirectory->getIso2Code(),
                'packstationNumber' => $facility->getData('shop_number'),
                'postNumber' => $facility->getData('post_number'),
            );
        }

        $postfilialeInfo = array();
        if ($facility->getData('shop_type') === PostalFacility::TYPE_POSTFILIALE) {
            $postfilialeInfo = array(
                'zip' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity(),
                'country' => $countryDirectory->getName(),
                'countryISOCode' => $countryDirectory->getIso2Code(),
                'postfilialNumber' => $facility->getData('shop_number'),
                'postNumber' => $facility->getData('post_number'),
            );
        }

        $parcelShopInfo = array();
        if ($facility->getData('shop_type') === PostalFacility::TYPE_PAKETSHOP) {
            $parcelShopInfo = array(
                'zip' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity(),
                'country' => $countryDirectory->getName(),
                'countryISOCode' => $countryDirectory->getIso2Code(),
                'parcelShopNumber' => $facility->getData('shop_number'),
                'streetName' => $street['street_name'],
                'streetNumber' => $street['street_number'],
            );
        }

        $receiverInfo = array(
            'name1' => $shippingAddress->getName(),
            'name2' => $shippingAddress->getCompany(),
            'streetName' => $street['street_name'],
            'streetNumber' => $street['street_number'],
            'addressAddition' => $street['supplement'],
            'zip' => $shippingAddress->getPostcode(),
            'city' => $shippingAddress->getCity(),
            'country' => $countryDirectory->getName(),
            'countryISOCode' => $countryDirectory->getIso2Code(),
            'state' => $shippingAddress->getRegion(),
            'phone' => $shippingAddress->getTelephone(),
            'email' => $shippingAddress->getEmail(),
            'packstation' => $packstationInfo,
            'postfiliale' => $postfilialeInfo,
            'parcelShop' => $parcelShopInfo,
        );
        $versendenInfo->getReceiver()->fromArray($receiverInfo, false);

        return $versendenInfo;
    }

    /**
     * @param ShipmentOrder $shipmentOrder
     * @return \Dhl\Versenden\Bcs\Api\Info
     */
    public function infoFromRequestData(ShipmentOrder $shipmentOrder)
    {
        $versendenInfo = new \Dhl\Versenden\Bcs\Api\Info();

        $packstationInfo = array();
        if ($shipmentOrder->getReceiver()->getPackstation()) {
            $packstationInfo = array(
                'zip' => $shipmentOrder->getReceiver()->getPackstation()->getZip(),
                'city' => $shipmentOrder->getReceiver()->getPackstation()->getCity(),
                'country' => $shipmentOrder->getReceiver()->getPackstation()->getCountry(),
                'countryISOCode' => $shipmentOrder->getReceiver()->getPackstation()->getCountryISOCode(),
                'packstationNumber' => $shipmentOrder->getReceiver()->getPackstation()->getPackstationNumber(),
                'postNumber' => $shipmentOrder->getReceiver()->getPackstation()->getPostNumber(),
            );
        }

        $postfilialeInfo = array();
        if ($shipmentOrder->getReceiver()->getPostfiliale()) {
            $postfilialeInfo = array(
                'zip' => $shipmentOrder->getReceiver()->getPostfiliale()->getZip(),
                'city' => $shipmentOrder->getReceiver()->getPostfiliale()->getCity(),
                'country' => $shipmentOrder->getReceiver()->getPostfiliale()->getCountry(),
                'countryISOCode' => $shipmentOrder->getReceiver()->getPostfiliale()->getCountryISOCode(),
                'postfilialNumber' => $shipmentOrder->getReceiver()->getPostfiliale()->getPostfilialNumber(),
                'postNumber' => $shipmentOrder->getReceiver()->getPostfiliale()->getPostNumber(),
            );
        }

        $parcelShopInfo = array();
        if ($shipmentOrder->getReceiver()->getParcelShop()) {
            $parcelShopInfo = array(
                'zip' => $shipmentOrder->getReceiver()->getParcelShop()->getZip(),
                'city' => $shipmentOrder->getReceiver()->getParcelShop()->getCity(),
                'country' => $shipmentOrder->getReceiver()->getParcelShop()->getCountry(),
                'countryISOCode' => $shipmentOrder->getReceiver()->getParcelShop()->getCountryISOCode(),
                'parcelShopNumber' => $shipmentOrder->getReceiver()->getParcelShop()->getParcelShopNumber(),
                'streetName' => $shipmentOrder->getReceiver()->getParcelShop()->getStreetName(),
                'streetNumber' => $shipmentOrder->getReceiver()->getParcelShop()->getStreetNumber(),
            );
        }

        $receiverInfo = array(
            'name1' => $shipmentOrder->getReceiver()->getName1(),
            'name2' => $shipmentOrder->getReceiver()->getName2(),
            'streetName' => $shipmentOrder->getReceiver()->getStreetName(),
            'streetNumber' => $shipmentOrder->getReceiver()->getStreetNumber(),
            'addressAddition' => $shipmentOrder->getReceiver()->getAddressAddition(),
            'dispatchingInformation' => $shipmentOrder->getReceiver()->getDispatchingInformation(),
            'zip' => $shipmentOrder->getReceiver()->getZip(),
            'city' => $shipmentOrder->getReceiver()->getCity(),
            'country' => $shipmentOrder->getReceiver()->getCountry(),
            'countryISOCode' => $shipmentOrder->getReceiver()->getCountryISOCode(),
            'state' => $shipmentOrder->getReceiver()->getState(),
            'phone' => $shipmentOrder->getReceiver()->getPhone(),
            'email' => $shipmentOrder->getReceiver()->getEmail(),
            'packstation' => $packstationInfo,
            'postfiliale' => $postfilialeInfo,
            'parcelShop' => $parcelShopInfo,

        );
        $versendenInfo->getReceiver()->fromArray($receiverInfo, false);

        $serviceInfo = array(
            'preferredDay' => $shipmentOrder->getServiceSelection()->getPreferredDay(),
            'preferredTime' => $shipmentOrder->getServiceSelection()->getPreferredTime(),
            'preferredLocation' => $shipmentOrder->getServiceSelection()->getPreferredLocation(),
            'preferredNeighbour' => $shipmentOrder->getServiceSelection()->getPreferredNeighbour(),
            'parcelAnnouncement' => $shipmentOrder->getServiceSelection()->getParcelAnnouncement(),
            'visualCheckOfAge' => $shipmentOrder->getServiceSelection()->getVisualCheckOfAge(),
            'returnShipment' => $shipmentOrder->getServiceSelection()->isReturnShipment(),
            'insurance' => $shipmentOrder->getServiceSelection()->getInsurance(),
            'bulkyGoods' => $shipmentOrder->getServiceSelection()->isBulkyGoods(),
            'cod' => $shipmentOrder->getServiceSelection()->getCod(),
            'printOnlyIfCodeable' => $shipmentOrder->getServiceSelection()->isPrintOnlyIfCodeable(),
        );
        $versendenInfo->getServices()->fromArray($serviceInfo, false);

        return $versendenInfo;
    }
}
