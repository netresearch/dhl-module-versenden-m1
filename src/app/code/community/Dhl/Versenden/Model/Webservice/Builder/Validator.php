<?php

/**
 * See LICENSE.md for license details.
 */


use Dhl\Versenden\Bcs\Api\Product;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData\ValidationException;

class Dhl_Versenden_Model_Webservice_Builder_Validator
{
    /**
     * @param Mage_Shipping_Model_Shipment_Request $shipmentRequest
     * @param ShipmentOrder $shipmentOrder
     * @throws ValidationException
     */
    public function validate(\Mage_Shipping_Model_Shipment_Request $shipmentRequest, ShipmentOrder $shipmentOrder)
    {
        $orderShipment = $shipmentRequest->getOrderShipment();

        $shippingProduct = $shipmentRequest->getData('gk_api_product');

        $insurance = $shipmentOrder->getServiceSelection()->getInsurance();
        $bulkyGoods = $shipmentOrder->getServiceSelection()->isBulkyGoods();
        $cod = $shipmentOrder->getServiceSelection()->getCod();
        $preferredDay = $shipmentOrder->getServiceSelection()->getPreferredDay();
        $visualCheckOfAge = $shipmentOrder->getServiceSelection()->getVisualCheckOfAge();

        $canShipPartially = empty($insurance) && empty($cod);
        $isPartial = ($orderShipment->getOrder()->getTotalQtyOrdered() != $orderShipment->getTotalQty());

        if (!$canShipPartially && $isPartial) {
            $message = 'Cannot do partial shipment with COD or Additional Insurance.';
            throw new ValidationException($message);
        }

        $canUseMerchandiseShipment = empty($insurance)
            && empty($bulkyGoods)
            && empty($cod)
            && empty($preferredDay)
            && empty($visualCheckOfAge);
        $isMerchandiseShipment = ($shippingProduct == Product::CODE_KLEINPAKET);

        if (!$canUseMerchandiseShipment && $isMerchandiseShipment) {
            $message = 'Kleinpaket cannot be booked with the services '
                . 'Additional Insurance, Bulky Goods, Cash on Delivery, Delivery Day, Visual Check of Age.';
            throw new ValidationException($message);
        }
    }
}
