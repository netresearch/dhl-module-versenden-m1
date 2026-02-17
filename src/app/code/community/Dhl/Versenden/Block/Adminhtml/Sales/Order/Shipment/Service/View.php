<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service\Type as Service;

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_View extends Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
{
    /**
     * Obtain the services that were chosen during shipment creation.
     *
     * COD is determined from the order's payment method rather than stored
     * service data, aligning with M2 behavior.
     *
     * @return \Dhl\Versenden\ParcelDe\Service\Collection
     */
    public function getServices()
    {
        $storeId = $this->getShipment()->getStoreId();
        $shippingAddress = $this->getShipment()->getShippingAddress();
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');

        $availableServices = $serviceConfig->getServices($storeId);

        $versendenInfo = $shippingAddress->getData('dhl_versenden_info');
        if (!$versendenInfo instanceof \Dhl\Versenden\ParcelDe\Info) {
            return $availableServices;
        }

        $selectedServices = [];

        /** @var Service\Generic $availableService */
        foreach ($availableServices as $availableService) {
            $code = $availableService->getCode();
            $availableService->setValue($versendenInfo->getServices()->{$code});
            if ($availableService->isSelected()) {
                $selectedServices[] = $availableService;
            }
        }

        // COD is determined by order payment method, not stored service data
        $order = $this->getShipment()->getOrder();
        $paymentMethod = $order->getPayment() ? $order->getPayment()->getMethod() : null;
        if ($paymentMethod && $shipmentConfig->isCodPaymentMethod($paymentMethod, $storeId)) {
            $codService = $availableServices->getItem(\Dhl\Versenden\ParcelDe\Service\Cod::CODE);
            if ($codService instanceof \Dhl\Versenden\ParcelDe\Service\Cod) {
                $codService->setValue('1');
                $codService->setDefaultValue('');
                $selectedServices[\Dhl\Versenden\ParcelDe\Service\Cod::CODE] = $codService;
            }
        }

        return new \Dhl\Versenden\ParcelDe\Service\Collection($selectedServices);
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
