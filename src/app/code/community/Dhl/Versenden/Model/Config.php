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
use Dhl\Versenden\Config;
use Dhl\Versenden\Config\Exception as ConfigException;
use Dhl\Versenden\Config\Service as ServiceConfig;
use Dhl\Versenden\Config\Shipment\Settings as ShipmentSettings;
use Dhl\Versenden\Config\Shipper;
use Dhl\Versenden\Config\Shipper\Account;
use Dhl\Versenden\Config\Shipper\Contact as ShipperContact;
use Dhl\Versenden\Config\Shipper\BankData;
use Dhl\Versenden\Config\Shipper\ReturnReceiver;
use Dhl\Versenden\Service;
use Dhl_Versenden_Model_Adminhtml_System_Config_Source_Yesoptno as ParcelAnnouncementOptions;
/**
 * Dhl_Versenden_Model_Config
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Config
{
    const CONFIG_XML_PATH_AUTOLOAD_ENABLED = 'dhl_versenden/dev/autoload_enabled';

    const CONFIG_XML_PATH_CARRIER = 'carriers/dhlversenden';
    const CONFIG_XML_PATH_CARRIER_ACTIVE = 'carriers/dhlversenden/active';
    const CONFIG_XML_PATH_CARRIER_TITLE = 'carriers/dhlversenden/title';
    const CONFIG_XML_PATH_SANDBOX_MODE = 'carriers/dhlversenden/sandbox_mode';
    const CONFIG_XML_PATH_LOGGING_ENABLED = 'carriers/dhlversenden/logging_enabled';
    const CONFIG_XML_PATH_LOG_LEVEL = 'carriers/dhlversenden/log_level';

    /**
     * Check if custom autoloader should be registered.
     *
     * @return bool
     */
    public function isAutoloadEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOLOAD_ENABLED);
    }

    /**
     * Obtain carrier title.
     *
     * @param mixed $store
     * @return string
     */
    public function getTitle($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CARRIER_TITLE, $store);
    }

    /**
     * Check if carrier is enabled for checkout.
     *
     * @param mixed $store
     * @return bool
     */
    public function isActive($store = null)
    {
        $carrierCode = Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE;
        $carrier     = Mage::getModel('shipping/config')->getCarrierInstance($carrierCode, $store);
        return $carrier->isActive();
    }

    /**
     * Check if carrier should request test labels only.
     *
     * @param mixed $store
     * @return bool
     */
    public function isSandboxModeEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SANDBOX_MODE, $store);
    }

    /**
     * Check if logging is enabled (global scope)
     *
     * @param int $level
     * @return bool
     */
    public function isLoggingEnabled($level = null)
    {
        $level = is_null($level) ? Zend_Log::DEBUG : $level;

        $isEnabled = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_LOGGING_ENABLED);
        $isLevelEnabled = (Mage::getStoreConfig(self::CONFIG_XML_PATH_LOG_LEVEL) >= $level);

        return ($isEnabled && $isLevelEnabled);
    }

    /**
     * Load the merchant's DHL account data.
     *
     * @param mixed $store
     * @return Account
     * @throws ConfigException
     */
    public function getShipperAccount($store = null)
    {
        $carrierConfig = Mage::getStoreConfig(self::CONFIG_XML_PATH_CARRIER, $store);

        $reader = new Config($carrierConfig);
        return new Account($reader);
    }

    /**
     * @param mixed $store
     * @return BankData
     * @throws ConfigException
     */
    public function getShipperBankData($store = null)
    {
        $carrierConfig = Mage::getStoreConfig(self::CONFIG_XML_PATH_CARRIER, $store);

        $reader = new Config($carrierConfig);
        return new BankData($reader);
    }

    /**
     * @param mixed $store
     * @return ShipperContact
     * @throws ConfigException
     */
    public function getShipperContact($store = null)
    {
        $carrierConfig = Mage::getStoreConfig(self::CONFIG_XML_PATH_CARRIER, $store);

        $country = Mage::getSingleton('directory/country')->loadByCode($carrierConfig['contact_countryid']);
        $carrierConfig['contact_country'] = $country->getName();
        $carrierConfig['contact_countrycode'] = $country->getIso2Code();

        $reader = new Config($carrierConfig);
        return new ShipperContact($reader);
    }

    /**
     * @param mixed $store
     * @return ShipperContact
     * @throws ConfigException
     */
    public function getReturnReceiver($store = null)
    {
        $carrierConfig = Mage::getStoreConfig(self::CONFIG_XML_PATH_CARRIER, $store);
        if ($carrierConfig['returnshipment_use_shipper']) {
            return $this->getShipperContact($store);
        }

        $country = Mage::getSingleton('directory/country')->loadByCode($carrierConfig['returnshipment_countryid']);
        $carrierConfig['returnshipment_country'] = $country->getName();
        $carrierConfig['returnshipment_countrycode'] = $country->getIso2Code();

        $reader = new Config($carrierConfig);
        return new ReturnReceiver($reader);
    }

    /**
     * Load the merchant's DHL account data.
     *
     * @param mixed $store
     * @return ShipmentSettings
     * @throws ConfigException
     */
    public function getShipmentSettings($store = null)
    {
        $carrierConfig = Mage::getStoreConfig(self::CONFIG_XML_PATH_CARRIER, $store);

        $reader = new Config($carrierConfig);
        return new ShipmentSettings($reader);
    }

    /**
     * Load the service configuration.
     *
     * @param mixed $store
     * @return ServiceConfig
     * @throws ConfigException
     */
    public function getServices($store = null)
    {
        $carrierConfig = Mage::getStoreConfig(self::CONFIG_XML_PATH_CARRIER, $store);

        $reader = new Config($carrierConfig);
        return new ServiceConfig($reader);
    }

    /**
     * @param mixed $store
     * @return Shipper
     * @throws ConfigException
     */
    public function getShipper($store = null)
    {
        return new Shipper(
            $this->getShipperAccount($store),
            $this->getShipperBankData($store),
            $this->getShipperContact($store),
            $this->getReturnReceiver($store)
        );
    }

    /**
     * Obtain the service objects that are enabled via module configuration.
     *
     * @param mixed $store
     * @return Service[]
     */
    public function getEnabledServices($store = null)
    {
        $services = [];
        $serviceConfig = $this->getServices($store);

        //TODO(nr): there must be a better way to do this
        $bgDefault = $serviceConfig->bulkyGoods;
        if ($bgDefault) {
            $services[]= new Service\BulkyGoods($bgDefault);
        }

        $plDefault = $serviceConfig->preferredLocation;
        if ($plDefault) {
            $services[]= new Service\PreferredLocation($plDefault);
        }

        $pnDefault = $serviceConfig->preferredNeighbour;
        if ($pnDefault) {
            $services[]= new Service\PreferredNeighbour($pnDefault);
        }

        $paDefault = $serviceConfig->parcelAnnouncement;
        if ($paDefault) {
            $paService = new Service\ParcelAnnouncement();

            if ($paDefault === ParcelAnnouncementOptions::Y) {
                $paService->setIsRequired();
            } elseif ($paDefault === ParcelAnnouncementOptions::OPT) {
                $paService->setIsOptional();
            }

            $services[]= $paService;
        }

        $dtDefault = $serviceConfig->deliveryTimeFrame;
        if ($dtDefault) {
            $services[]= new Service\DeliveryTimeFrame($dtDefault);
        }

        return $services;
    }
}
