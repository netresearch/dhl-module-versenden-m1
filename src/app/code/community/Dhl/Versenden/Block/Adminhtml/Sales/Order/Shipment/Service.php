<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Shipment\Service\Type as Service;

abstract class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
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
     * @return \Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic[]
     */
    abstract public function getServices();

    /**
     * @param Service\Generic $service
     * @return Service\Renderer
     */
    abstract public function getRenderer(Service\Generic $service);
}
