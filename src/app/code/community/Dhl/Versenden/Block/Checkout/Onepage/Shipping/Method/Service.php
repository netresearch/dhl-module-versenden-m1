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
use \Dhl\Versenden\Shipment\Service\Type\Generic as Service;
/**
 * Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service
    extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Obtain the services that are enabled via config and can be chosen by customer.
     *
     * @return Service[]
     */
    public function getServices()
    {
        $collection = Mage::getModel('dhl_versenden/config_service')->getEnabledServices();
        $services = $collection->getItems();
        $services = array_filter(
            $services, function (Service $service) {
                return $service->isCustomerService();
            }
        );

        return $services;
    }

    /**
     * Obtain the shipping methods that should be processed with DHL Versenden.
     *
     * @return string json encoded methods array
     */
    public function getDhlMethods()
    {
        $config = Mage::getModel('dhl_versenden/config_shipment');
        $dhlMethods = $config->getSettings()->getShippingMethods();
        return $this->helper('core/data')->jsonEncode($dhlMethods);
    }
}
