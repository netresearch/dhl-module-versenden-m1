<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Model_Webservice_Builder_Service
{
    /** @var Dhl_Versenden_Model_Config_Shipper */
    protected $_shipperConfig;

    /** @var Dhl_Versenden_Model_Config_Shipment */
    protected $_shipmentConfig;

    /** @var Dhl_Versenden_Model_Config_Service */
    protected $_serviceConfig;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Service constructor.
     * @param stdClass[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'shipper_config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipper) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_shipperConfig = $args[$argName];

        $argName = 'shipment_config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipment) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_shipmentConfig = $args[$argName];

        $argName = 'service_config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Service) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_serviceConfig = $args[$argName];
    }

    /**
     * Build service selection using the SDK request builder
     *
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @param mixed[] $serviceInfo
     * @return void
     */
    public function build(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder, Mage_Core_Model_Abstract $salesEntity, array $serviceInfo)
    {
        // Handle empty service info gracefully (e.g., in tests without services)
        $selectedServices = $serviceInfo['shipment_service'] ?? [];
        $serviceDetails = $serviceInfo['service_setting'] ?? [];

        // Preferred Day
        if (!empty($selectedServices[Service\PreferredDay::CODE])) {
            $sdkBuilder->setPreferredDay($serviceDetails[Service\PreferredDay::CODE]);
        }

        // Preferred Location
        if (!empty($selectedServices[Service\PreferredLocation::CODE])) {
            $sdkBuilder->setPreferredLocation($serviceDetails[Service\PreferredLocation::CODE]);
        }

        // Preferred Neighbour
        if (!empty($selectedServices[Service\PreferredNeighbour::CODE])) {
            $sdkBuilder->setPreferredNeighbour($serviceDetails[Service\PreferredNeighbour::CODE]);
        }

        // Visual Check of Age (checkbox enables, dropdown provides value)
        if (!empty($selectedServices[Service\VisualCheckOfAge::CODE])) {
            $sdkBuilder->setVisualCheckOfAge($serviceDetails[Service\VisualCheckOfAge::CODE]);
        }

        // Bulky Goods
        if (!empty($selectedServices[Service\BulkyGoods::CODE])) {
            $sdkBuilder->setBulkyGoods();
        }

        // Parcel Outlet Routing
        if (!empty($selectedServices[Service\ParcelOutletRouting::CODE])) {
            // Prefer form-submitted email, then config email, then order email
            $formEmail = !empty($serviceDetails[Service\ParcelOutletRouting::CODE])
                ? $serviceDetails[Service\ParcelOutletRouting::CODE]
                : '';
            if ($formEmail) {
                $routingEmail = $formEmail;
            } else {
                $configEmail = $this->_serviceConfig->getParcelOutletNotificationEmail($salesEntity->getStoreId());
                $routingEmail = $configEmail ?: $salesEntity->getShippingAddress()->getEmail();
            }
            $sdkBuilder->setParcelOutletRouting($routingEmail);
        }

        // Cash on Delivery
        $payment = $salesEntity->getPayment();
        $paymentMethod = $payment ? $payment->getMethod() : null;
        if ($paymentMethod && $this->_shipmentConfig->isCodPaymentMethod($paymentMethod, $salesEntity->getStoreId())) {
            $codAmount = (float) number_format($salesEntity->getBaseGrandTotal(), 2, '.', '');
            $sdkBuilder->setCodAmount($codAmount);
        }

        // ========================================================================
        // International shipment services
        // ========================================================================

        // Endorsement (checkbox enables, dropdown provides value)
        if (!empty($selectedServices[Service\Endorsement::CODE])) {
            $sdkBuilder->setShipmentEndorsementType($serviceDetails[Service\Endorsement::CODE]);
        }

        // Postal Delivery Duty Paid (pDDP) — required for US/PR shipments up to
        // the active value limit, forbidden above it (limit switches 2026-07-24)
        $pddpSelected = !empty($selectedServices[Service\PostalDeliveryDutyPaid::CODE]);
        if ($pddpSelected) {
            $this->_assertPddpAllowed($salesEntity);
        }

        $shouldEnablePddp = $pddpSelected || $this->_shouldAutoEnablePddp($salesEntity);

        if ($shouldEnablePddp) {
            $sdkBuilder->setDeliveryDutyPaid();
        }

        // Delivery Type (Economy/Premium selection)
        if (!empty($selectedServices[Service\DeliveryType::CODE])) {
            $deliveryType = $serviceDetails[Service\DeliveryType::CODE] ?? '';
            switch ($deliveryType) {
                case Service\DeliveryType::ECONOMY:
                    $sdkBuilder->setDeliveryType(
                        \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_ECONOMY,
                    );
                    break;
                case Service\DeliveryType::PREMIUM:
                    $sdkBuilder->setDeliveryType(
                        \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_PREMIUM,
                    );
                    break;
                case Service\DeliveryType::CDP:
                    $sdkBuilder->setDeliveryType(
                        \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_CDP,
                    );
                    break;
            }
        }

        // Closest Drop Point from checkout overrides Delivery Type to CDP.
        // In the packaging popup, admin sees DeliveryType locked to CDP and submits
        // deliveryType=CDP (handled above). For autocreate, the customer's checkout
        // selection is stored as closestDropPoint=true and needs explicit mapping.
        if (!empty($selectedServices[Service\ClosestDropPoint::CODE])) {
            $sdkBuilder->setDeliveryType(
                \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_CDP,
            );
        }

        // GoGreen Plus (carbon-neutral shipping)
        if (!empty($selectedServices[Service\GoGreenPlus::CODE])) {
            $sdkBuilder->setGoGreenPlus();
        }

        // GoGreen Plus for return shipment label
        if (!empty($selectedServices[Service\GoGreenPlus::CODE])
            && !empty($selectedServices[Service\ReturnShipment::CODE])
        ) {
            $sdkBuilder->setReturnShipmentGoGreenPlus();
        }

        // Additional Insurance (with specific value)
        if (!empty($selectedServices[Service\AdditionalInsurance::CODE])) {
            $insuredValue = (float) $salesEntity->getBaseGrandTotal();
            $sdkBuilder->setInsuredValue($insuredValue);
        }

        // ========================================================================
        // Delivery restriction services
        // ========================================================================

        // Named Person Only - Delivery only to named recipient (no family/household)
        if (!empty($selectedServices[Service\NamedPersonOnly::CODE])) {
            $sdkBuilder->setNamedPersonOnly();
        }

        // Signed For By Recipient - Require recipient signature
        if (!empty($selectedServices[Service\SignedForByRecipient::CODE])) {
            $sdkBuilder->setSignedForByRecipient();
        }

        // No Neighbour Delivery - Prohibit delivery to neighbours
        if (!empty($selectedServices[Service\NoNeighbourDelivery::CODE])) {
            $sdkBuilder->setNoNeighbourDelivery();
        }

        // Note: IdentCheck service has been deprecated and removed.
    }

    /**
     * Check if PDDP should be auto-enabled for this shipment
     *
     * PDDP is automatically enabled for shipments into the US customs territory
     * (USA, Puerto Rico) with customs value up to and including the active
     * threshold. The threshold switches on 2026-07-24 (see PddpThresholds).
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @return bool
     */
    protected function _shouldAutoEnablePddp(Mage_Core_Model_Abstract $salesEntity)
    {
        $threshold = $this->_getPddpThreshold($salesEntity);
        if ($threshold === null) {
            // not a US territory destination or unsupported base currency
            return false;
        }

        // DHL: up to and including the limit (bis einschließlich)
        $customsValue = (float) $salesEntity->getBaseGrandTotal();
        return $customsValue <= $threshold;
    }

    /**
     * Reject manually selected pDDP that the DHL API would refuse anyway.
     *
     * Above the active limit the API rejects pDDP shipments with an unhelpful
     * error; failing here gives the admin an actionable message instead.
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @return void
     * @throws InvalidArgumentException
     */
    protected function _assertPddpAllowed(Mage_Core_Model_Abstract $salesEntity)
    {
        if (!$this->_isUsTerritoryDestination($salesEntity)) {
            // thresholds only apply to the US customs territory
            return;
        }

        $currencyCode = $this->_getBaseCurrencyCode($salesEntity);
        $threshold = Dhl_Versenden_Model_Util_PddpThresholds::forCurrency(
            $currencyCode,
            $this->_getCurrentTimestamp(),
        );
        if ($threshold === null) {
            throw new \InvalidArgumentException(
                Mage::helper('dhl_versenden/data')->__('PDDP is only available for stores with EUR or USD as base currency.')
            );
        }

        $customsValue = (float) $salesEntity->getBaseGrandTotal();
        if ($customsValue > $threshold) {
            throw new \InvalidArgumentException(
                sprintf(
                    Mage::helper('dhl_versenden/data')->__('PDDP is not allowed for USA/Puerto Rico shipments with value > %s %s. The DHL API will reject shipments with PDDP enabled.'),
                    number_format($threshold, 2),
                    $currencyCode,
                ),
            );
        }
    }

    /**
     * Check if the shipment destination is in the US customs territory (USA, Puerto Rico)
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @return bool
     */
    protected function _isUsTerritoryDestination(Mage_Core_Model_Abstract $salesEntity)
    {
        $shippingAddress = $salesEntity->getShippingAddress();
        if (!$shippingAddress) {
            return false;
        }

        return in_array(
            $shippingAddress->getCountryId(),
            Dhl_Versenden_Model_Util_UsCustomsTerritory::COUNTRY_CODES,
            true,
        );
    }

    /**
     * Resolve the active pDDP threshold for this shipment, if applicable
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @return float|null threshold or null (not US territory / unsupported currency)
     */
    protected function _getPddpThreshold(Mage_Core_Model_Abstract $salesEntity)
    {
        if (!$this->_isUsTerritoryDestination($salesEntity)) {
            return null;
        }

        return Dhl_Versenden_Model_Util_PddpThresholds::forCurrency(
            $this->_getBaseCurrencyCode($salesEntity),
            $this->_getCurrentTimestamp(),
        );
    }

    /**
     * Obtain the current unix timestamp (accessor for testability)
     *
     * @return int
     */
    protected function _getCurrentTimestamp()
    {
        return time();
    }

    /**
     * Obtain the store's base currency code (accessor for testability)
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @return string
     */
    protected function _getBaseCurrencyCode(Mage_Core_Model_Abstract $salesEntity)
    {
        return Mage::app()->getStore($salesEntity->getStoreId())->getBaseCurrencyCode();
    }
}
