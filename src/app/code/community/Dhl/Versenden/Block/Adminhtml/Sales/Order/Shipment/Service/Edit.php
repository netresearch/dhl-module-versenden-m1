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
use \Dhl\Versenden\Shipment\Service\Type as Service;
/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit
    extends Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
{
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

        $enabledServices = $serviceConfig->getServices($storeId);

        $shippingInfoJson = $shippingAddress->getData('dhl_versenden_info');
        $shippingInfoObj = json_decode($shippingInfoJson);
        $shippingInfo = \Dhl\Versenden\Webservice\RequestData\ObjectMapper::getShippingInfo((object)$shippingInfoObj);
        if ($shippingInfo !== null) {
            $serviceSelection = $shippingInfo->getServiceSelection();
            $serviceConfig->setServiceValues($enabledServices, $serviceSelection);
        }


        $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $storeId));

        $shippingProducts = Product::getCodesByCountry($shipperCountry, $recipientCountry, $euCountries);
        $isPostalFacility = $this->helper('dhl_versenden/data')->isPostalFacility($shippingAddress);

        $filter = new \Dhl\Versenden\Shipment\Service\Filter($shippingProducts, $isPostalFacility, false);
        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        return $filteredCollection;
    }

    /**
     * @param Service\Generic $service
     * @return Service\Renderer
     */
    public function getRenderer(Service\Generic $service)
    {
        $renderer = new Service\Renderer($service);
        return $renderer;
    }
}
