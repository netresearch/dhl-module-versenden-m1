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
use \Dhl\Versenden\Product;
/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging
{
    /**
     * Check if services need to be rendered at all.
     *
     * @return string
     */
    public function renderView()
    {
        $shippingMethod = $this->getShipment()->getOrder()->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            return '';
        }

        return parent::renderView();
    }

    /**
     * Obtain the services that are enabled via config. Customer's service
     * selection from checkout is read from shipping address.
     *
     * @return \Dhl\Versenden\Shipment\Service\Type\Generic[]
     */
    public function getServices()
    {
        $storeId = $this->getShipment()->getStoreId();
        $shippingAddress = $this->getShipment()->getShippingAddress();
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');

        $enabledServices = $serviceConfig->getEnabledServices();

        $shippingInfoJson = $shippingAddress->getData('dhl_versenden_info');
        $shippingInfoObj = json_decode($shippingInfoJson);
        $shippingInfo = \Dhl\Versenden\Webservice\RequestData\ObjectMapper::getShippingInfo((object)$shippingInfoObj);
        if ($shippingInfo !== null) {
            $serviceSelection = $shippingInfo->getServiceSelection();
            $serviceConfig->setServiceValues($enabledServices, $serviceSelection);
        }

        
        $shipperCountry = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $storeId));

        $shippingProducts = Product::getCodesByCountry($shipperCountry, $recipientCountry, $euCountries);
        $isPostalFacility = $this->helper('dhl_versenden/webservice')->isPostalFacility($shippingAddress);

        $filter = new \Dhl\Versenden\Shipment\Service\Filter($shippingProducts, $isPostalFacility, false);
        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        return $filteredCollection;
    }
}
