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
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\GlobalSettings;
/**
 * Dhl_Versenden_Model_Webservice_Builder_Shipper
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
