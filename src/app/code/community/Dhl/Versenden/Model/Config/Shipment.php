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
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\GlobalSettings;

/**
 * Dhl_Versenden_Model_Config_Shipment
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Config_Shipment extends Dhl_Versenden_Model_Config
{
    const CONFIG_XML_FIELD_PRINTONLYIFCODEABLE = 'shipment_printonlyifcodeable';
    const CONFIG_XML_FIELD_UNITOFMEASURE = 'shipment_unitofmeasure';
    const CONFIG_XML_FIELD_DHLMETHODS = 'shipment_dhlmethods';
    const CONFIG_XML_FIELD_CODMETHODS = 'shipment_dhlcodmethods';

    /**
     * @param mixed $store
     * @return GlobalSettings
     */
    public function getSettings($store = null)
    {
        $printOnlyIfCodeable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PRINTONLYIFCODEABLE, $store);
        $unitOfMeasure = $this->getStoreConfig(self::CONFIG_XML_FIELD_UNITOFMEASURE, $store);

        $shippingMethods = $this->getStoreConfig(self::CONFIG_XML_FIELD_DHLMETHODS, $store);
        if (empty($shippingMethods)) {
            $shippingMethods = array();
        } else {
            $shippingMethods = explode(',', $shippingMethods);
        }

        $codPaymentMethods = $this->getStoreConfig(self::CONFIG_XML_FIELD_CODMETHODS, $store);
        if (empty($codPaymentMethods)) {
            $codPaymentMethods = array();
        } else {
            $codPaymentMethods = explode(',', $codPaymentMethods);
        }

        /**
         * TODO(nr): do not create RequestData objects while reading from config.
         * instead, read plain values and convert prior to actual webservice call.
         */
        return new GlobalSettings(
            $printOnlyIfCodeable,
            $unitOfMeasure,
            $shippingMethods,
            $codPaymentMethods,
            ShipmentOrder::LABEL_TYPE_B64
        );
    }

    /**
     * Check if the given shipping method should be processed with DHL Versenden.
     *
     * @param string $shippingMethod
     * @param mixed $store
     * @return bool
     */
    public function canProcessMethod($shippingMethod, $store = null)
    {
        if (!in_array($this->getShipperCountry($store), array('AT', 'DE'))) {
            // shipper country is not supported, regardless of shipping method.
            return false;
        }

        $configuredMethods = $this->getSettings($store)->getShippingMethods();
        foreach ($configuredMethods as $method) {
            if (false !== strpos($shippingMethod, $method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the given payment method is cash on delivery.
     *
     * @param string $paymentMethod
     * @param mixed $store
     * @return bool
     */
    public function isCodPaymentMethod($paymentMethod, $store = null)
    {
        return in_array($paymentMethod, $this->getSettings($store)->getCodPaymentMethods());
    }
}
