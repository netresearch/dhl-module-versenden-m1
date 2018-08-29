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
use \Mage_Checkout_Block_Onepage_Abstract as Onepage_Abstract;

/**
 * Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service extends Onepage_Abstract
{
    /**
     * @var Dhl_Versenden_Model_Config
     */
    private $config;

    /**
     * @var Dhl_Versenden_Model_Config_Service
     */
    private $serviceConfig;

    /**
     * @var Dhl_Versenden_Model_Config_Shipment
     */
    private $shipmentConfig;

    /**
     * @var Dhl_Versenden_Helper_Data
     */
    private $helper;

    /**
     * @var Dhl_Versenden_Model_Services_Processor
     */
    private $serviceProcessor;

    /**
     * Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service constructor.
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->config = Mage::getModel('dhl_versenden/config');
        $this->serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $this->shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $this->helper = $this->helper('dhl_versenden/data');
        $this->serviceProcessor = Mage::getModel(
            'dhl_versenden/services_processor',
            array('quote' => $this->getQuote())
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
            $storeId
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
        } else {
            return false;
        }
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
     * Obtain Frontend Service hint based on service code.
     *
     * @param string $serviceCode
     * @return string
     */
    public function getServiceHint($serviceCode)
    {
        switch ($serviceCode) {
            case Service\PreferredDay::CODE:
                $msg = 'Choose one of the displayed days as your preferred day for your parcel delivery.'
                    . ' Other days are not possible due to delivery processes.';
                break;
            case Service\PreferredTime::CODE:
                $msg = 'Indicate a preferred time, which suits you best for your parcel delivery by'
                    . ' choosing one of the displayed time windows.';
                break;
            case Service\PreferredLocation::CODE:
                $msg = 'Choose a weather-protected and non-visible place on your property'
                    . ' where we can deposit the parcel in your absence.';
                break;
            case Service\PreferredNeighbour::CODE:
                $msg = 'Determine a person in your immediate neighborhood whom we can hand out your parcel.'
                    . ' This person should live in the same building, directly opposite or next door.';
                break;
            default:
                $msg = '';
        }

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
        /** @var DHL_Versenden_Model_Config_Service $serviceConfig */
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');
        switch ($serviceCode) {
            case Service\PreferredDay::CODE:
                $msg = $serviceConfig->getPrefDayHandlingFeeText($this->getQuote()->getStoreId());
                break;
            case Service\PreferredTime::CODE:
                $msg = $serviceConfig->getPrefTimeHandlingFeeText($this->getQuote()->getStoreId());
                break;
            default:
                $msg = '';
        }

        return $msg ? $this->__($msg): '';
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
     * @return bool
     */
    public function isDayAndTime()
    {
        $services = $this->getServices();
        $time = $services->getItem(Service\PreferredTime::CODE);
        $day = $services->getItem(Service\PreferredDay::CODE);
        $time = $time ? true : false;
        $day = $day ? true : false;

        return $time && $day;
    }

    /**
     * @return string
     */
    public function getDayAndTimeFeeText()
    {
        /** @var DHL_Versenden_Model_Config_Service $serviceConfig */
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $msg = $serviceConfig->getPrefDayAndTimeHandlingFeeText($this->getQuote()->getStoreId());

        return $msg;
    }

    /**
     * @param string $html
     * @param string $serviceCode
     * @return string
     */
    public function addNoneOption($html, $serviceCode)
    {
        $span = $serviceCode === Service\PreferredDay::CODE ? '<span>-</span>' : '';
        $title = $serviceCode === Service\PreferredDay::CODE ? 'none day' : 'none time';
        $noneOption = '<div>'.
            '<input type="radio" name="service_setting['.$serviceCode.']" '.
            'id="shipment_service_'.$serviceCode.'_none" value="" checked="checked">'.
            '<label for="shipment_service_'.$serviceCode.'_none" '.
            'title="'.$this->helper('dhl_versenden/data')->__($title).'">'.
            $span.
            '<span>'.$this->helper('dhl_versenden/data')->__($title).'</span></label>'.
            '</div>';

        return $noneOption.$html;
    }

    /**
     * @param \Dhl\Versenden\Bcs\Api\Info $versendenInfo
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
        } else {
            return false;
        }
    }
}
