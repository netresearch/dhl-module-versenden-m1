<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\Data\GlobalSettings;
use Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface;
use Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\OrderConfiguration;

class Dhl_Versenden_Model_Webservice_Builder_Settings
{
    /** @var Dhl_Versenden_Model_Config_Shipment */
    protected $_config;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Settings constructor.
     * @param Dhl_Versenden_Model_Config[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipment) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_config = $args[$argName];
    }

    /**
     * @param mixed $store
     * @return GlobalSettings
     */
    public function getSettings($store)
    {
        return $this->_config->getSettings($store);
    }

    /**
     * Build order configuration for REST API from store settings.
     *
     * Extracts label format settings from store config and creates an OrderConfiguration
     * object for use with the REST shipment client.
     *
     * @param mixed $store Store ID or store model
     * @return OrderConfigurationInterface Order configuration with label format settings
     */
    public function build($store)
    {
        $settings = $this->_config->getSettings($store);

        // Map SOAP GlobalSettings to REST OrderConfiguration
        $printOnlyIfCodeable = $settings->isPrintOnlyIfCodeable();

        // Label type is B64 for REST API (PDF base64 encoded)
        // Doc format is PDF, print format is configurable via system configuration
        $docFormat = OrderConfigurationInterface::DOC_FORMAT_PDF;
        $printFormat = $this->_config->getPrintFormat($store);

        return new OrderConfiguration(
            $printOnlyIfCodeable,  // mustEncode
            false,                  // combinedPrinting
            $docFormat,             // docFormat
            $printFormat,           // printFormat
            null,                   // printFormatReturn
            null,                    // profile (use default)
        );
    }
}
