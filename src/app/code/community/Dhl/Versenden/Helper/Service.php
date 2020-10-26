<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Helper_Service extends Mage_Core_Helper_Abstract
{
    /**
     * @return string
     */
    public function setDetailValidation()
    {
        if ($this->isLoctionOrneighbourEnabled()) {
            return 'js/dhl_versenden/validationDetail.js';
        }

        return '';
    }

    /**
     * @return string
     */
    public function setSpecialValidation()
    {
        if ($this->isLoctionOrneighbourEnabled()) {
            return 'js/dhl_versenden/validationSpecial.js';
        }

        return '';
    }

    /**
     * @return string
     */
    public function setServiceObserver()
    {
        if ($this->isLocationAndNeighbourEnabled()) {
            return 'js/dhl_versenden/excludeService.js';
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isLoctionOrneighbourEnabled()
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
     * @return bool
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
