<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\GlobalSettings;

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
        if ($this->getShipperCountry($store) !== 'DE') {
            // shipper country is not supported, regardless of shipping method.
            return false;
        }

        $configuredMethods = array_filter($this->getSettings($store)->getShippingMethods());
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
