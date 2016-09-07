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
use \Dhl\Versenden\Shipment\Service as Service;
use \Dhl\Versenden\Shipment\Service\Type\Generic as ServiceItem;
use \Dhl\Versenden\Product;
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
     * @return Service\Collection
     */
    public function getServices()
    {
        $storeId = $this->getQuote()->getStoreId();
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');

        $enabledServices = $serviceConfig->getEnabledServices($storeId);


        $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $storeId));

        $shippingProducts = Product::getCodesByCountry($shipperCountry, $recipientCountry, $euCountries);
        $isPostalFacility = $this->helper('dhl_versenden/data')->isPostalFacility($shippingAddress);

        $filter = new \Dhl\Versenden\Shipment\Service\Filter($shippingProducts, $isPostalFacility, true);
        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        return $filteredCollection;
    }

    /**
     * Obtain the shipping methods that should be processed with DHL Versenden.
     *
     * @return string json encoded methods array
     */
    public function getDhlMethods()
    {
        $storeId = $this->getQuote()->getStoreId();

        $config = Mage::getModel('dhl_versenden/config_shipment');
        $dhlMethods = $config->getSettings($storeId)->getShippingMethods();
        return $this->helper('core/data')->jsonEncode($dhlMethods);
    }
}
