<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;
use Dhl\Versenden\ParcelDe\Service\Collection;
use Mage_Checkout_Block_Onepage_Abstract as Onepage_Abstract;

/**
 * See LICENSE.md for license details.
 */
class Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service extends Onepage_Abstract
{
    private const HEADLINES = [
        Service\NoNeighbourDelivery::CODE => 'No Neighbour Delivery: Discreet Dispatch',
        Service\GoGreenPlus::CODE         => 'GoGreen Plus: Climate Neutral Shipping',
        Service\ParcelAnnouncement::CODE  => 'Parcel Announcement: Shipment status notifications',
        Service\ClosestDropPoint::CODE    => 'Closest Drop Point: Delivery to your nearest drop-off point',
    ];

    private const HINTS = [
        Service\PreferredDay::CODE        => 'Choose one of the displayed days as your delivery day for your parcel delivery. Other days are not possible due to delivery processes.',
        Service\PreferredLocation::CODE   => 'Choose a weather-protected and non-visible place on your property where we can deposit the parcel in your absence.',
        Service\PreferredNeighbour::CODE  => 'Determine a person in your immediate neighborhood whom we can hand out your parcel. This person should live in the same building, directly opposite or next door.',
        Service\NoNeighbourDelivery::CODE => 'Your parcel will only be delivered to you personally. No delivery to neighbors will take place.',
        Service\GoGreenPlus::CODE         => 'Ship your parcel climate-neutrally with GoGreen Plus. DHL offsets the CO2 emissions generated during transport.',
        Service\ParcelAnnouncement::CODE  => 'DHL will notify the recipient about the shipment status via email.',
        Service\ClosestDropPoint::CODE    => 'Your parcel will be delivered to the nearest drop-off point. You will be notified about the exact location.',
    ];

    /**
     * @var Dhl_Versenden_Model_Config
     */
    protected $config;

    /**
     * @var Dhl_Versenden_Model_Config_Service
     */
    protected $serviceConfig;

    /**
     * @var Dhl_Versenden_Model_Config_Shipment
     */
    protected $shipmentConfig;

    /**
     * @var Dhl_Versenden_Helper_Data
     */
    protected $helper;

    /**
     * @var Dhl_Versenden_Model_Services_Processor
     */
    protected $serviceProcessor;

    /**
     * Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service constructor.
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->config = Mage::getModel('dhl_versenden/config');
        $this->serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $this->shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $this->helper = $this->helper('dhl_versenden/data');
        $this->serviceProcessor = Mage::getModel(
            'dhl_versenden/services_processor',
            ['quote' => $this->getQuote()],
        );

        parent::__construct($args);
    }

    /**
     * Obtain the services that are enabled via config and can be chosen by customer.
     *
     * @return Service\Collection
     */
    public function getServices()
    {
        $storeId = $this->getQuote()->getStoreId();
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $shipperCountry = $this->config->getShipperCountry($storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $isPostalFacility = $this->helper->isPostalFacility($shippingAddress);

        /** @var Service\Collection $availableServices */
        $availableServices = $this->serviceConfig->getAvailableServices(
            $shipperCountry,
            $recipientCountry,
            $isPostalFacility,
            true,
            $storeId,
        );

        return $this->serviceProcessor->processServices($availableServices);
    }

    /**
     * Check if the shipping address is already a dhl location
     *
     * @return boolean
     */
    public function isShippingAddressDHLLocation()
    {
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $dhlStationType = $shippingAddress->getData('dhl_station_type');
        $dhlVersendenInfo = $shippingAddress->getData('dhl_versenden_info');

        if (!empty($dhlStationType)
            || (!empty($dhlVersendenInfo)
                && $this->isReceiverLocationUsed($shippingAddress->getData('dhl_versenden_info')))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Obtain the shipping methods that should be processed with DHL Versenden.
     *
     * @return string json encoded methods array
     */
    public function getDhlMethods()
    {
        $storeId = $this->getQuote()->getStoreId();
        $dhlMethods = $this->shipmentConfig->getSettings($storeId)->getShippingMethods();

        return $this->helper('core/data')->jsonEncode($dhlMethods);
    }

    /**
     * Obtain checkout headline for service grouping display.
     *
     * @param string $serviceCode
     * @return string
     */
    public function getServiceHeadline($serviceCode)
    {
        $msg = self::HEADLINES[$serviceCode] ?? '';

        return $msg ? $this->__($msg) : '';
    }

    /**
     * Obtain Frontend Service hint based on service code.
     *
     * @param string $serviceCode
     * @return string
     */
    public function getServiceHint($serviceCode)
    {
        $msg = self::HINTS[$serviceCode] ?? '';

        return $msg ? $this->__($msg) : '';
    }

    /**
     * Obtain Frontend Service Fee hint based on service code.
     *
     * Note: text comes from module config, set translations there.
     *
     * @param $serviceCode
     * @return string
     */
    public function getServiceFeeText($serviceCode)
    {
        switch ($serviceCode) {
            case Service\PreferredDay::CODE:
                $msg = $this->serviceConfig->getPrefDayHandlingFeeText($this->getQuote()->getStoreId());
                break;
            case Service\ClosestDropPoint::CODE:
                $msg = $this->serviceConfig->getCdpHandlingFeeText($this->getQuote()->getStoreId());
                break;
            case Service\NoNeighbourDelivery::CODE:
                $msg = $this->serviceConfig->getNoNeighbourDeliveryHandlingFeeText($this->getQuote()->getStoreId());
                break;
            case Service\GoGreenPlus::CODE:
                $msg = $this->serviceConfig->getGoGreenHandlingFeeText($this->getQuote()->getStoreId());
                break;
            default:
                $msg = '';
        }

        return $msg ? $this->__($msg) : '';
    }

    /**
     * Inject a tooltip icon into a label HTML element.
     *
     * @param string $html  Label HTML containing a closing </label> tag
     * @param string $tooltip  Tooltip text (empty string = no injection)
     * @return string
     */
    public function injectTooltip($html, $tooltip)
    {
        if ($tooltip === '') {
            return $html;
        }

        return str_replace(
            '</label>',
            sprintf('<i class="tooltip-inner" data-tooltip="%s">?</i></label>', $tooltip),
            $html,
        );
    }

    /**
     * @return bool
     */
    public function isOnePageCheckout()
    {
        $layoutHandles = $this->getLayout()->getUpdate()->getHandles();
        foreach ($layoutHandles as $layoutHandle) {
            if (strpos($layoutHandle, 'checkout_onepage') !== false) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param \Dhl\Versenden\ParcelDe\Info $versendenInfo
     *
     * @return boolean
     */
    protected function isReceiverLocationUsed($versendenInfo)
    {
        if ($versendenInfo->getReceiver()->packstation->packstationNumber
            || $versendenInfo->getReceiver()->postfiliale->postfilialNumber
            || $versendenInfo->getReceiver()->parcelShop->parcelShopNumber
        ) {
            return true;
        }

        return false;
    }
}
