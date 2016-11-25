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
use \Dhl\Versenden\Bcs\Api\Shipment\Service\Type as Service;
/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_View
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_View
    extends Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
{
    /**
     * Obtain the services that were chosen during shipment creation.
     *
     * @return \Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic[]
     */
    public function getServices()
    {
        $storeId = $this->getShipment()->getStoreId();
        $shippingAddress = $this->getShipment()->getShippingAddress();
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');

        $availableServices = $serviceConfig->getServices($storeId);

        $versendenInfo = $shippingAddress->getData('dhl_versenden_info');
        if (!$versendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            return $availableServices;
        }

        $selectedServices = array();

        /** @var Service\Generic $availableService */
        foreach ($availableServices as $availableService) {
            $code = $availableService->getCode();
            $availableService->setValue($versendenInfo->getServices()->{$code});
            if ($availableService->isSelected()) {
                $selectedServices[]= $availableService;
            }
        }

        return new Dhl\Versenden\Bcs\Api\Shipment\Service\Collection($selectedServices);
    }

    /**
     * @param Service\Generic $service
     * @return Service\Renderer
     */
    public function getRenderer(Service\Generic $service)
    {
        $renderer = new Service\Renderer($service, true);
        $renderer->setSelectedYes($this->__('Yes'));
        $renderer->setSelectedNo($this->__('No'));
        return $renderer;
    }
}
