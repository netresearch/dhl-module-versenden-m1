<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Shipment\Service\Type as Service;

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
