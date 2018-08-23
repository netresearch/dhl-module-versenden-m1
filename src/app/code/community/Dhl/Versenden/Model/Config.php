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
    const CONFIG_SECTION = 'carriers';
    const CONFIG_GROUP = 'dhlversenden';


    const CONFIG_XML_PATH_AUTOLOAD_ENABLED = 'dhl_versenden/dev/autoload_enabled';

    const CONFIG_XML_PATH_CARRIER_TITLE    = 'title';
    const CONFIG_XML_PATH_SANDBOX_MODE     = 'sandbox_mode';
    const CONFIG_XML_PATH_LOGGING_ENABLED  = 'logging_enabled';
    const CONFIG_XML_PATH_LOG_LEVEL        = 'log_level';

    const CONFIG_XML_PATH_WS_AUTH_USERNAME = 'webservice_auth_username';
    const CONFIG_XML_PATH_WS_AUTH_PASSWORD = 'webservice_auth_password';

    const CONFIG_XML_PATH_SANDBOX_ENDPOINT      = 'sandbox_endpoint';
    const CONFIG_XML_PATH_SANDBOX_AUTH_USERNAME = 'sandbox_auth_username';
    const CONFIG_XML_PATH_SANDBOX_AUTH_PASSWORD = 'sandbox_auth_password';

    const CONFIG_XML_PATH_PARCELMANAGEMENT_ENPOINT = 'parcelmanagement_endpoint';
    const CONFIG_XML_PATH_PARCELMANAGEMENT_SANDBOX_ENDPOINT = 'parcelmanagement_sandbox_endpoint';

    const CONFIG_XML_PATH_SENDRECEIVERPHONE     = 'shipment_sendreceiverphone';

    const CONFIG_XML_PATH_AUTOCREATE_ENABLED      = 'shipment_autocreate_enabled';
    const CONFIG_XML_PATH_AUTOCREATE_ORDER_STATUS = 'shipment_autocreate_order_status';
    const CONFIG_XML_PATH_AUTOCREATE_NOTIFY_CUSTOMER = 'shipment_autocreate_notify_customer';
    const CONFIG_XML_PATH_EXCLUDED_DROP_OFF_DAYS = 'drop_off_days';

    const CONFIG_XML_PATH_CHECKOUT_TRACKING_ENBLED = 'checkout/dhl_versenden/checkout_tracking_enabled';
    const CONFIG_XML_PATH_CHECKOUT_TRACKING_NEXT_EXEC = 'checkout/dhl_versenden/checkout_tracking_next_exec';
    const CONFIG_XML_PATH_CHECKOUT_TRACKING_INTERVAL = 'checkout/dhl_versenden/checkout_tracking_interval';


    /**
     * Wrap store config access.
     *
     * @param string $field
     * @param mixed $store
     * @return mixed
     */
    protected function getStoreConfig($field, $store = null)
    {
        $path = sprintf('%s/%s/%s', self::CONFIG_SECTION, self::CONFIG_GROUP, $field);
        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Wrap store config access.
     *
     * @param string $field
     * @param mixed $store
     * @return bool
     */
    protected function getStoreConfigFlag($field, $store = null)
    {
        $path = sprintf('%s/%s/%s', self::CONFIG_SECTION, self::CONFIG_GROUP, $field);
        return Mage::getStoreConfigFlag($path, $store);
    }

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
        return $this->getStoreConfig(self::CONFIG_XML_PATH_CARRIER_TITLE, $store);
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
     * @return bool
     */
    public function isSandboxModeEnabled()
    {
        return $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SANDBOX_MODE);
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

        $isEnabled = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_LOGGING_ENABLED);
        $isLevelEnabled = ($this->getStoreConfig(self::CONFIG_XML_PATH_LOG_LEVEL) >= $level);

        return ($isEnabled && $isLevelEnabled);
    }

    /**
     * Obtain the webservice endpoint address (location).
     *
     * @return string Null in production mode: use default from WSDL.
     */
    public function getEndpoint()
    {
        if ($this->isSandboxModeEnabled()) {
            return $this->getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_ENDPOINT);
        }

        return null;
    }

    /**
     * Obtain username for CIG authentication.
     *
     * @return string
     */
    public function getWebserviceAuthUsername()
    {
        if ($this->isSandboxModeEnabled()) {
            return $this->getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_AUTH_USERNAME);
        }

        return $this->getStoreConfig(self::CONFIG_XML_PATH_WS_AUTH_USERNAME);
    }

    /**
     * Obtain password for CIG authentication.
     *
     * @return string
     */
    public function getWebserviceAuthPassword()
    {
        if ($this->isSandboxModeEnabled()) {
            return $this->getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_AUTH_PASSWORD);
        }

        return $this->getStoreConfig(self::CONFIG_XML_PATH_WS_AUTH_PASSWORD);
    }

    /**
     * Obtain shipper country from shipping origin configuration.
     *
     * @param mixed $store
     * @return string
     */
    public function getShipperCountry($store = null)
    {
        $shipperCountry = Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $store
        );

        return $shipperCountry;
    }


    /**
     * Check if shipment auto creation is enabled.
     *
     * @param mixed $store
     * @return bool
     */
    public function isShipmentAutoCreateEnabled($store = null)
    {
        return $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_ENABLED, $store);
    }

    /**
     * Obtain the status of orders applicable for shipment auto creation.
     *
     * @param mixed $store
     * @return string[]
     */
    public function getAutoCreateOrderStatus($store = null)
    {
        $status = $this->getStoreConfig(self::CONFIG_XML_PATH_AUTOCREATE_ORDER_STATUS, $store);
        return explode(',', $status);
    }

    /**
     * Check if Customer notification is enabled
     *
     * @param mixed $store
     * @return bool
     */
    public function isAutoCreateNotifyCustomer($store = null)
    {
        return $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_NOTIFY_CUSTOMER, $store);
    }

    /**
     * Check if receiver's phone number should be sent to DHL
     *
     * @param mixed $store
     * @return bool
     */
    public function isSendReceiverPhone($store = null)
    {
        return $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SENDRECEIVERPHONE, $store);
    }

    /**
     * Check if inclusion of dhl tracking pixel is enabled.
     *
     * @param null $store
     * @return bool
     */
    public function isTrackingEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_CHECKOUT_TRACKING_ENBLED, $store);
    }

    /**
     * Get Parcelmanagement API Endpoint
     * @return string
     */
    public function getParcelManagementEndpoint()
    {
        if ($this->isSandboxModeEnabled()) {
            return $this->getStoreConfig(self::CONFIG_XML_PATH_PARCELMANAGEMENT_SANDBOX_ENDPOINT);
        }

        return $this->getStoreConfig(self::CONFIG_XML_PATH_PARCELMANAGEMENT_ENPOINT);
    }

    /**
     * @param null $scopeId
     * @return string
     */
    public function getExcludedDropOffDays($scopeId = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_EXCLUDED_DROP_OFF_DAYS, $scopeId);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getNextTrackDate($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CHECKOUT_TRACKING_NEXT_EXEC, $store);
    }

    /**
     * @param string $date
     */
    public function setNextTrackDate($date)
    {
        /** @var Mage_Core_Model_Config $coreConfig */
        $coreConfig = Mage::getModel('core/config');
        $coreConfig->saveConfig(self::CONFIG_XML_PATH_CHECKOUT_TRACKING_NEXT_EXEC, $date, 'default');
    }

    /**
     * @param null $store
     * @return string
     */
    public function getTrackingInterval($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CHECKOUT_TRACKING_INTERVAL, $store);
    }
}
