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
use Dhl\Versenden\Config\Shipment\Settings as ShipmentSettings;
use Dhl\Versenden\Config\Shipper;
use Dhl\Versenden\Config\Shipper\Account;
use Dhl\Versenden\Config\Shipper\Contact as ShipperContact;
use Dhl\Versenden\Config\Shipper\BankData;
use Dhl\Versenden\Config\Shipper\ReturnReceiver;
use Dhl\Versenden\Service;
use Dhl\Versenden\Service\Collection as ServiceCollection;
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

    const CONFIG_XML_PATH_SERVICE_DAYOFDELIVERY = 'carriers/dhlversenden/service_dayofdelivery_enabled';
    const CONFIG_XML_PATH_SERVICE_DELIVERYTIMEFRAME = 'carriers/dhlversenden/service_deliverytimeframe_enabled';
    const CONFIG_XML_PATH_SERVICE_PREFERREDLOCATION = 'carriers/dhlversenden/service_preferredlocation_enabled';
    const CONFIG_XML_PATH_SERVICE_PREFERREDLOCATION_PLACEHOLDER
        = 'carriers/dhlversenden/service_preferredlocation_placeholder';
    const CONFIG_XML_PATH_SERVICE_PREFERREDNEIGHBOUR = 'carriers/dhlversenden/service_preferredneighbour_enabled';
    const CONFIG_XML_PATH_SERVICE_PREFERREDNEIGHBOUR_PLACEHOLDER
        = 'carriers/dhlversenden/service_preferredneighbour_placeholder';
    const CONFIG_XML_PATH_SERVICE_PACKSTATION = 'carriers/dhlversenden/service_packstation_enabled';
    const CONFIG_XML_PATH_SERVICE_PARCELANNOUNCEMENT = 'carriers/dhlversenden/service_parcelannouncement_enabled';
    const CONFIG_XML_PATH_SERVICE_VISUALCHECKOFAGE = 'carriers/dhlversenden/service_visualcheckofage_enabled';
    const CONFIG_XML_PATH_SERVICE_RETURNSHIPMENT = 'carriers/dhlversenden/service_returnshipment_enabled';
    const CONFIG_XML_PATH_SERVICE_INSURANCE = 'carriers/dhlversenden/service_insurance_enabled';
    const CONFIG_XML_PATH_SERVICE_BULKYGOODS = 'carriers/dhlversenden/service_bulkygoods_enabled';

    const CONFIG_XML_PATH_WS_AUTH_USERNAME = 'carriers/dhlversenden/webservice_auth_username';
    const CONFIG_XML_PATH_WS_AUTH_PASSWORD = 'carriers/dhlversenden/webservice_auth_password';

    const CONFIG_XML_PATH_SANDBOX_ENDPOINT = 'carriers/dhlversenden/sandbox_endpoint';
    const CONFIG_XML_PATH_SANDBOX_USER = 'carriers/dhlversenden/sandbox_account_user';
    const CONFIG_XML_PATH_SANDBOX_SIGNATURE = 'carriers/dhlversenden/sandbox_account_signature';
    const CONFIG_XML_PATH_SANDBOX_EKP = 'carriers/dhlversenden/sandbox_account_ekp';
    const CONFIG_XML_PATH_SANDBOX_DHLPAKET = 'carriers/dhlversenden/sandbox_account_participation_dhlpaket';
    const CONFIG_XML_PATH_SANDBOX_RETURNSHIPMENT = 'carriers/dhlversenden/sandbox_account_participation_returnshipment';
    const CONFIG_XML_PATH_SANDBOX_GOGREEN_ENABLED = 'sandbox_account_gogreen_enabled';

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
        $level = ($level === null) ? Zend_Log::DEBUG : $level;

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
     * Check if the given shipping method should be processed with DHL Versenden.
     *
     * @param string $shippingMethod
     * @return bool
     */
    public function canProcessMethod($shippingMethod)
    {
        $enabledMethods = $this->getShipmentSettings()->shippingMethods;

        return in_array($shippingMethod, $enabledMethods);
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
     * Load the service configuration.
     *
     * @param mixed $store
     * @return ServiceCollection
     */
    public function getServices($store = null)
    {
        $collection = new ServiceCollection();

        $dayOfDelivery = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_DAYOFDELIVERY, $store);
        $collection->addItem(new Service\DayOfDelivery($dayOfDelivery));

        $deliveryTimeFrame = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_DELIVERYTIMEFRAME, $store);
        $collection->addItem(new Service\DeliveryTimeFrame($deliveryTimeFrame, [
            '10001200' => '10:00 - 12:00',
            '12001400' => '12:00 - 14:00',
            '14001600' => '14:00 - 16:00',
            '16001800' => '16:00 - 18:00',
            '18002000' => '18:00 - 20:00',
            '19002100' => '19:00 - 21:00',
        ]));

        $preferredLocation = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_PREFERREDLOCATION, $store);
        $placeholder = Mage::getStoreConfig(self::CONFIG_XML_PATH_SERVICE_PREFERREDLOCATION_PLACEHOLDER, $store);
        $placeholder = Mage::helper('dhl_versenden/data')->__($placeholder);
        $collection->addItem(new Service\PreferredLocation($preferredLocation, $placeholder));

        $preferredNeighbour = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_PREFERREDNEIGHBOUR, $store);
        $placeholder = Mage::getStoreConfig(self::CONFIG_XML_PATH_SERVICE_PREFERREDNEIGHBOUR_PLACEHOLDER, $store);
        $placeholder = Mage::helper('dhl_versenden/data')->__($placeholder);
        $collection->addItem(new Service\PreferredNeighbour($preferredNeighbour, $placeholder));

        $parcelAnnouncement = Mage::getStoreConfig(self::CONFIG_XML_PATH_SERVICE_PARCELANNOUNCEMENT, $store);
        $collection->addItem(new Service\ParcelAnnouncement($parcelAnnouncement));

        $visualCheckOfAge = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_VISUALCHECKOFAGE, $store);
        $collection->addItem(new Service\VisualCheckOfAge($visualCheckOfAge, [
            Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
            Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
        ]));

        $returnShipment = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_RETURNSHIPMENT, $store);
        $collection->addItem(new Service\ReturnShipment($returnShipment));

        $insurance = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_INSURANCE, $store);
        $collection->addItem(new Service\Insurance($insurance, [
            Service\Insurance::TYPE_A => '2.500',
            Service\Insurance::TYPE_B => '25.000'
        ]));

        $bulkyGoods = Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SERVICE_BULKYGOODS, $store);
        $collection->addItem(new Service\BulkyGoods($bulkyGoods));

        return $collection;
    }

    /**
     * Obtain the service objects that are enabled via module configuration.
     * Services are initialized with their enabled status. Additionally, set
     * the display mode for parcel announcement.
     *
     * @param mixed $store
     * @return ServiceCollection
     */
    public function getEnabledServices($store = null)
    {
        $services = $this->getServices($store)->getItems();

        $items = array_filter($services, function (Service $item) {
            return (bool)$item->value;
        });

        return new ServiceCollection($items);
    }

    /**
     * Obtain username for CIG authentication.
     *
     * @return string
     */
    public function getWebserviceAuthUsername()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_WS_AUTH_USERNAME);
    }

    /**
     * Obtain password for CIG authentication.
     *
     * @return string
     */
    public function getWebserviceAuthPassword()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_WS_AUTH_PASSWORD);
    }

    /**
     * Obtain the webservice endpoint address (location).
     *
     * @param mixed $store
     * @return string Null in production mode: use default from WSDL.
     */
    public function getEndpoint($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_ENDPOINT, $store);
        }

        return null;
    }

    /**
     * Obtain Business Customer Portal username.
     *
     * @param mixed $store
     * @return string
     */
    public function getAuthenticationUser($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_USER, $store);
        }

        //TODO(nr): obtain production credentials
        return null;
    }

    /**
     * Obtain Business Customer Portal password/signature.
     *
     * @param mixed $store
     * @return string
     */
    public function getAuthenticationSignature($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_SIGNATURE, $store);
        }

        //TODO(nr): obtain production credentials
        return null;
    }
}
