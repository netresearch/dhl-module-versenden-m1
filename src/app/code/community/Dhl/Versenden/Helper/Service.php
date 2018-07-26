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
 * @author    Andreas Mülleer <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      httpy://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Helper_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     httpy://www.netresearch.de/
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
        /**
         * @var Dhl_Versenden_Model_Config_Service $config
         */
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
        /**
         * @var Dhl_Versenden_Model_Config_Service $config
         */
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
