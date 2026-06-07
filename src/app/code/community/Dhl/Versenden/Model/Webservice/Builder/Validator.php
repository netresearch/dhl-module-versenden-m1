<?php

/**
 * See LICENSE.md for license details.
 */


use Dhl\Versenden\ParcelDe\Product;
use Dhl\Versenden\ParcelDe\Service;
use Dhl\Versenden\ParcelDe\Config\ValidationException;

class Dhl_Versenden_Model_Webservice_Builder_Validator
{
    /**
     * Validate shipment request business rules.
     *
     * Updated for REST SDK: extracts service data from Magento request and replicates
     * ServiceBuilder's COD detection logic. Validates BEFORE SDK build (fail fast).
     *
     * Note: COD detection mirrors ServiceBuilder.php:119-123. If ServiceBuilder's
     * COD logic changes, this validator must be updated accordingly.
     *
     * @param Mage_Shipping_Model_Shipment_Request $shipmentRequest
     * @throws ValidationException
     */
    public function validate(\Mage_Shipping_Model_Shipment_Request $shipmentRequest)
    {
        $orderShipment = $shipmentRequest->getOrderShipment();
        $shippingProduct = $shipmentRequest->getData('gk_api_product');

        if (empty($shippingProduct)) {
            throw new ValidationException("GK API Product is not set" . $orderShipment?->getOrder()?->getIncrementId() ?? "n/a");
        }

        // Extract services from Magento request data (matches ServiceBuilder structure)
        $serviceInfo = $shipmentRequest->getData('services') ?? [];
        $selectedServices = $serviceInfo['shipment_service'] ?? [];

        $hasInsurance = $this->isServiceSelected($selectedServices, Service\AdditionalInsurance::CODE);
        $hasBulkyGoods = $this->isServiceSelected($selectedServices, Service\BulkyGoods::CODE);
        $hasPreferredDay = $this->isServiceSelected($selectedServices, Service\PreferredDay::CODE);
        $hasVisualCheckOfAge = $this->isServiceSelected($selectedServices, Service\VisualCheckOfAge::CODE);

        // COD detection - replicates ServiceBuilder.php:119-123
        // COD is determined by payment method configuration, NOT by service selection
        $paymentMethod = $orderShipment->getOrder()->getPayment()->getMethod();
        $storeId = $orderShipment->getStoreId();
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $hasCod = $shipmentConfig->isCodPaymentMethod($paymentMethod, $storeId);

        // Rule 1: Partial shipments cannot have COD or Insurance
        $canShipPartially = !$hasInsurance && !$hasCod;
        $isPartial = ((float) $orderShipment->getOrder()->getTotalQtyOrdered() !== (float) $orderShipment->getTotalQty());

        if (!$canShipPartially && $isPartial) {
            $message = 'Cannot do partial shipment with COD or Additional Insurance.';
            throw new ValidationException($message);
        }

        // Rule 2: Kleinpaket (V62KP) cannot have premium services
        $canUseMerchandiseShipment = !$hasInsurance
            && !$hasBulkyGoods
            && !$hasCod
            && !$hasPreferredDay
            && !$hasVisualCheckOfAge;
        $isMerchandiseShipment = ($shippingProduct === Product::CODE_KLEINPAKET);

        if (!$canUseMerchandiseShipment && $isMerchandiseShipment) {
            $message = 'Kleinpaket cannot be booked with the services '
                . 'Additional Insurance, Bulky Goods, Cash on Delivery, Delivery Day, Visual Check of Age.';
            throw new ValidationException($message);
        }

        // Rule 3: Mutual exclusion - preferredNeighbour and noNeighbourDelivery
        $hasPreferredNeighbour = $this->isServiceSelected($selectedServices, Service\PreferredNeighbour::CODE);
        $hasNoNeighbourDelivery = $this->isServiceSelected($selectedServices, Service\NoNeighbourDelivery::CODE);

        if ($hasPreferredNeighbour && $hasNoNeighbourDelivery) {
            $message = 'Preferred Neighbour and No Neighbour Delivery services are mutually exclusive. '
                . 'Please select only one of these options.';
            throw new ValidationException($message);
        }
    }

    /**
     * @param array<string, mixed> $selectedServices Service codes mapped to their submitted form value
     * @param string $code Service code to look up
     * @return bool true if the service was submitted with a truthy value
     */
    private function isServiceSelected(array $selectedServices, $code)
    {
        return (bool) ($selectedServices[$code] ?? false);
    }
}
