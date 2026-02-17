<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\Data\GlobalSettings;
use Dhl\Versenden\ParcelDe\Config\Data\LabelType;

class Dhl_Versenden_Model_Config_Shipment extends Dhl_Versenden_Model_Config
{
    public const CONFIG_XML_FIELD_PRINTONLYIFCODEABLE = 'shipment_printonlyifcodeable';
    public const CONFIG_XML_FIELD_UNITOFMEASURE = 'shipment_unitofmeasure';
    public const CONFIG_XML_FIELD_PRINTFORMAT = 'shipment_printformat';
    public const CONFIG_XML_FIELD_DHLMETHODS = 'shipment_dhlmethods';
    public const CONFIG_XML_FIELD_CODMETHODS = 'shipment_dhlcodmethods';

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
            $shippingMethods = [];
        } else {
            $shippingMethods = explode(',', $shippingMethods);
        }

        $codPaymentMethods = $this->getStoreConfig(self::CONFIG_XML_FIELD_CODMETHODS, $store);
        if (empty($codPaymentMethods)) {
            $codPaymentMethods = [];
        } else {
            $codPaymentMethods = explode(',', $codPaymentMethods);
        }

        // phpcs:disable Generic.Commenting.Todo.TaskFound
        /**
         * TODO(nr): do not create RequestData objects while reading from config.
         * instead, read plain values and convert prior to actual webservice call.
         */
        // phpcs:enable Generic.Commenting.Todo.TaskFound
        return new GlobalSettings(
            $printOnlyIfCodeable,
            $unitOfMeasure,
            $shippingMethods,
            $codPaymentMethods,
            LabelType::LABEL_TYPE_B64,
        );
    }

    /**
     * Get configured label print format.
     *
     * Returns the print format configured in system configuration.
     * Defaults to A4 format if not configured.
     *
     * @param mixed $store
     * @return string Print format code (e.g., 'A4', '910-300-700', etc.)
     */
    public function getPrintFormat($store = null)
    {
        $printFormat = $this->getStoreConfig(self::CONFIG_XML_FIELD_PRINTFORMAT, $store);

        // Default to A4 if not configured
        if (empty($printFormat)) {
            return \Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface::PRINT_FORMAT_A4;
        }

        return $printFormat;
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
        if (empty($shippingMethod)) {
            return false;
        }

        if ($this->getShipperCountry($store) !== 'DE') {
            // shipper country is not supported, regardless of shipping method.
            return false;
        }

        $configuredMethods = array_filter($this->getSettings($store)->getShippingMethods());
        foreach ($configuredMethods as $method) {
            if (false !== strpos($shippingMethod, (string) $method)) {
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
        if (empty($paymentMethod)) {
            return false;
        }

        return in_array($paymentMethod, $this->getSettings($store)->getCodPaymentMethods());
    }
}
