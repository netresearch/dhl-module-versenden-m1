<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Helper_Service extends Mage_Core_Helper_Abstract
{
    const PREFERRED_SERVICE_ANY_ENABLED = 'is_dhl_preferred_neighbor_or_location_enabled';
    const PREFERRED_SERVICE_ALL_ENABLED = 'is_dhl_preferred_neighbor_and_location_enabled';

    /**
     * Obtain condition flag for use in layout.
     *
     * @return string
     */
    public function getServiceDetailsValidationCondition()
    {
        return self::PREFERRED_SERVICE_ANY_ENABLED;
    }

    /**
     * Obtain condition flag for use in layout.
     *
     * @return string
     */
    public function getInputSpecialCharsValidationCondition()
    {
        return self::PREFERRED_SERVICE_ANY_ENABLED;
    }

    /**
     * Obtain condition flag for use in layout.
     *
     * @return string
     */
    public function getServiceCombinationValidationCondition()
    {
        return self::PREFERRED_SERVICE_ALL_ENABLED;
    }

    /**
     * Check if either preferred location or preferred neighbour are enabled in config.
     *
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isLocationOrNeighbourEnabled()
    {
        /** @var Dhl_Versenden_Model_Config_Service $config */
        $config = Mage::getModel('dhl_versenden/config_service');
        $services = $config->getEnabledServices(Mage::app()->getStore()->getStoreId())->getItems();
        $location = \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation::CODE;
        $neighbour = \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredNeighbour::CODE;
        if (array_key_exists($location, $services) || array_key_exists($neighbour, $services)) {
            return true;
        }

        return false;
    }

    /**
     * Check if both, preferred location and preferred neighbour are enabled in config.
     *
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isLocationAndNeighbourEnabled()
    {
        /** @var Dhl_Versenden_Model_Config_Service $config */
        $config = Mage::getModel('dhl_versenden/config_service');
        $services = $config->getEnabledServices(Mage::app()->getStore()->getStoreId())->getItems();
        $location = \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation::CODE;
        $neighbour = \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredNeighbour::CODE;
        if (array_key_exists($location, $services) && array_key_exists($neighbour, $services)) {
            return true;
        }

        return false;
    }
}
