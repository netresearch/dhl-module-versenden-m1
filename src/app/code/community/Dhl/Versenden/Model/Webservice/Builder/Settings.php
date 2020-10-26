<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\GlobalSettings;

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
}
