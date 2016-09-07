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
use Dhl\Versenden\Service\Type as Service;
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
    const CONFIG_SECTION = 'carriers';
    const CONFIG_GROUP = 'dhlversenden';


    const CONFIG_XML_PATH_AUTOLOAD_ENABLED = 'dhl_versenden/dev/autoload_enabled';

    const CONFIG_XML_PATH_CARRIER = 'carriers/dhlversenden';
    const CONFIG_XML_PATH_CARRIER_ACTIVE = 'carriers/dhlversenden/active';
    const CONFIG_XML_PATH_CARRIER_TITLE = 'carriers/dhlversenden/title';
    const CONFIG_XML_PATH_SANDBOX_MODE = 'carriers/dhlversenden/sandbox_mode';
    const CONFIG_XML_PATH_SANDBOX_ENDPOINT = 'carriers/dhlversenden/sandbox_endpoint';
    const CONFIG_XML_PATH_LOGGING_ENABLED = 'carriers/dhlversenden/logging_enabled';
    const CONFIG_XML_PATH_LOG_LEVEL = 'carriers/dhlversenden/log_level';

    const CONFIG_XML_PATH_WS_AUTH_USERNAME = 'carriers/dhlversenden/webservice_auth_username';
    const CONFIG_XML_PATH_WS_AUTH_PASSWORD = 'carriers/dhlversenden/webservice_auth_password';

    const CONFIG_XML_PATH_SHIPPING_ORDER_STATUS       = 'shipment_creation_order_status';
    const CONFIG_XML_PATH_SHIPPING_PARCELANNOUNCEMENT = 'shipment_creation_parcelannouncement';
    const CONFIG_XML_PATH_SHIPPING_VISUALCHECKAGE     = 'shipment_creation_visualcheckofage';
    const CONFIG_XML_PATH_SHIPPING_RETURNSHIPMENT     = 'shipment_creation_returnshipment';
    const CONFIG_XML_PATH_SHIPPING_INSURANCE          = 'shipment_creation_insurance';
    const CONFIG_XML_PATH_SHIPPING_BULKYGOODS         = 'shipment_creation_bulkygoods';

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
     * Obtain configured order status for auto creation of shipping
     *
     * @param mixed $store
     * @return string
     */
    public function getAutocreateAllowedStatusCodes($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_ORDER_STATUS, $store);
    }

    /**
     * Obtain parcel announcement configuration for auto creation of shipments
     *
     * @param null $store
     * @return mixed
     */
    public function getParcelAnnouncmentConfig($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_PARCELANNOUNCEMENT, $store);
    }

    /**
     * Obtain visul check age config for auto creation of shipments
     *
     * @param null $store
     * @return mixed
     */
    public function getVisualCheckAgeConfig($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_VISUALCHECKAGE, $store);
    }

    /**
     * Obtain return shipments config for auto creation of shipment
     *
     * @param null $store
     * @return mixed
     */
    public function getReturnShipmentConfig($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_RETURNSHIPMENT, $store);
    }

    /**
     * Obtain insurance config for auto creation of shipment
     *
     * @param null $store
     * @return mixed
     */
    public function getInsuranceConfig($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_INSURANCE, $store);
    }

    /**
     * Obtain bulkygoods config cor auto creation of shipment
     *
     * @param null $store
     * @return mixed
     */
    public function getBulkyGoodsConfig($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_BULKYGOODS, $store);
    }
}
