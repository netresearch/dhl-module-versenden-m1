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
use \Dhl\Versenden\Bcs\Api\Shipment\Service as Service;

/**
 * Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service
    extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Obtain the services that are enabled via config and can be chosen by customer.
     *
     * @return Service\Collection
     */
    public function getServices()
    {
        $storeId = $this->getQuote()->getStoreId();
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');

        $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $isPostalFacility = $this->helper('dhl_versenden/data')->isPostalFacility($shippingAddress);

        $availableServices = $serviceConfig->getAvailableServices(
            $shipperCountry,
            $recipientCountry,
            $isPostalFacility,
            true,
            $storeId
        );

        return $availableServices;
    }

    /**
     * Check if the shipping address is already a dhl location
     *
     * @return boolean
     */
    public function isShippingAddressDHLLocation()
    {
        $shippingAddress = $this->getQuote()->getShippingAddress();

        if (!empty($shippingAddress->getData('dhl_station_type'))
            || (!empty($shippingAddress->getData('dhl_versenden_info'))
                && $this->isReceiverLocationUsed($shippingAddress->getData('dhl_versenden_info')))
        ) {
            return true;
        } else {
            return false;
        }
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

    /**
     * Obtain the shipping methods that should be processed with DHL Versenden.
     *
     * @return string json encoded methods array
     */
    public function getDhlMethods()
    {
        $storeId = $this->getQuote()->getStoreId();

        $config = Mage::getModel('dhl_versenden/config_shipment');
        $dhlMethods = $config->getSettings($storeId)->getShippingMethods();
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
                $msg = 'Choose a weather-protected and non-visible place on your property,'
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
}
