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
use \Dhl\Versenden\Bcs\Api\Shipment\Service;
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
     * selection from checkout is read from shipping address. Services from
     * config are added.
     *
     * @return Service\Type\Generic[]
     */
    public function getServices()
    {
        // obtain enabled services
        $storeId = $this->getShipment()->getStoreId();
        $shippingAddress = $this->getShipment()->getShippingAddress();
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');

        $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $isPostalFacility = $this->helper('dhl_versenden/data')->isPostalFacility($shippingAddress);

        $availableServices = $serviceConfig->getAvailableServices(
            $shipperCountry,
            $recipientCountry,
            $isPostalFacility,
            false,
            $storeId
        );

        // set default service values from config
        $printOnlyIfCodeable = Mage::getModel('dhl_versenden/config_shipment')->getSettings($storeId)
            ->isPrintOnlyIfCodeable();
        $availableServices->getItem(Service\PrintOnlyIfCodeable::CODE)->setValue($printOnlyIfCodeable);

        $parcelAnnouncement = $availableServices->getItem(Service\ParcelAnnouncement::CODE);
        if (($parcelAnnouncement instanceof Service\ParcelAnnouncement) && !$parcelAnnouncement->isCustomerService()) {
            $availableServices->getItem(Service\ParcelAnnouncement::CODE)->setValue(true);
        }

        // set/override service values from pre-selection
        /** @var \Dhl\Versenden\Bcs\Api\Info $versendenInfo */
        $versendenInfo = $shippingAddress->getData('dhl_versenden_info');
        if (!$versendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            return $availableServices;
        }

        /** @var Service\Type\Generic $availableService */
        foreach ($availableServices as $availableService) {
            $code = $availableService->getCode();
            $serviceSelection = $versendenInfo->getServices()->{$code};
            if ($serviceSelection !== null) {
                $availableService->setValue($serviceSelection);
            }
        }

        return $availableServices;
    }

    /**
     * @param Service\Type\Generic $service
     * @return Service\Type\Renderer
     */
    public function getRenderer(Service\Type\Generic $service)
    {
        $renderer = new Service\Type\Renderer($service);
        return $renderer;
    }
}
