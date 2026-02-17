<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility;

class Dhl_Versenden_Model_Info_Builder
{
    /**
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $shippingAddress
     * @param mixed[] $serviceInfo
     * @param mixed $store
     * @return \Dhl\Versenden\ParcelDe\Info
     */
    public function infoFromSales(
        Mage_Customer_Model_Address_Abstract $shippingAddress,
        array $serviceInfo,
        $store = null
    ) {
        $versendenInfo = new \Dhl\Versenden\ParcelDe\Info();
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
            $store,
        );

        /** @var \Dhl\Versenden\ParcelDe\Service\Type\Generic $availableService */
        foreach ($availableServices as $availableService) {
            $code = $availableService->getCode();
            if (isset($selectedServices[$code])) {
                $details = isset($selectedSettings[$code]) ? $selectedSettings[$code] : (bool) $selectedServices[$code];
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
            'dhl_versenden_fetch_postal_facility',
            [
                'customer_address'   => $shippingAddress,
                'postal_facility' => $facility,
            ],
        );

        $packstationInfo = [];
        if ($facility->getData('shop_type') === PostalFacility::TYPE_PACKSTATION) {
            $packstationInfo = [
                'zip' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity(),
                'country' => $countryDirectory->getName(),
                'countryISOCode' => $countryDirectory->getIso2Code(),
                'packstationNumber' => $facility->getData('shop_number'),
                'postNumber' => $facility->getData('post_number'),
            ];
        }

        $postfilialeInfo = [];
        if ($facility->getData('shop_type') === PostalFacility::TYPE_POSTFILIALE) {
            $postfilialeInfo = [
                'zip' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity(),
                'country' => $countryDirectory->getName(),
                'countryISOCode' => $countryDirectory->getIso2Code(),
                'postfilialNumber' => $facility->getData('shop_number'),
                'postNumber' => $facility->getData('post_number'),
            ];
        }

        $parcelShopInfo = [];
        if ($facility->getData('shop_type') === PostalFacility::TYPE_PAKETSHOP) {
            $parcelShopInfo = [
                'zip' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity(),
                'country' => $countryDirectory->getName(),
                'countryISOCode' => $countryDirectory->getIso2Code(),
                'parcelShopNumber' => $facility->getData('shop_number'),
                'streetName' => $street['street_name'],
                'streetNumber' => $street['street_number'],
            ];
        }

        $receiverInfo = [
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
        ];
        $versendenInfo->getReceiver()->fromArray($receiverInfo, false);

        return $versendenInfo;
    }

}
