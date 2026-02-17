<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Model_Webservice_Builder_Service
{
    /**
     * PDDP auto-enable threshold for USD (US shipments)
     */
    public const PDDP_THRESHOLD_USD = 800;

    /**
     * PDDP auto-enable threshold for EUR (US shipments from EUR stores)
     */
    public const PDDP_THRESHOLD_EUR = 680;

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

        // Postal Delivery Duty Paid (pDDP) - required for US shipments under 800 USD
        // Auto-enable for USA shipments < $800 if configured
        $shouldEnablePddp = !empty($selectedServices[Service\PostalDeliveryDutyPaid::CODE])
            || $this->_shouldAutoEnablePddp($salesEntity);

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
     * PDDP is automatically enabled for USA shipments with customs value under the
     * applicable threshold, when the auto-enable feature is configured.
     * Thresholds: 800 USD or 680 EUR (approximate equivalent).
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @return bool
     */
    protected function _shouldAutoEnablePddp(Mage_Core_Model_Abstract $salesEntity)
    {
        // Get shipping address
        $shippingAddress = $salesEntity->getShippingAddress();
        if (!$shippingAddress) {
            return false;
        }

        // Check if destination is USA
        $destCountry = $shippingAddress->getCountryId();
        if ($destCountry !== 'US') {
            return false;
        }

        // Determine threshold based on store's base currency
        $baseCurrency = Mage::app()->getStore($salesEntity->getStoreId())->getBaseCurrencyCode();
        $threshold = $this->_getPddpThresholdForCurrency($baseCurrency);
        if ($threshold === null) {
            // Unsupported base currency - do not auto-enable
            return false;
        }

        // Check if customs value is under the threshold
        $customsValue = (float) $salesEntity->getBaseGrandTotal();
        return $customsValue < $threshold;
    }

    /**
     * Get PDDP auto-enable threshold for the given currency
     *
     * @param string $currencyCode
     * @return float|null Threshold value or null if currency not supported
     */
    protected function _getPddpThresholdForCurrency($currencyCode)
    {
        $thresholds = [
            'USD' => self::PDDP_THRESHOLD_USD,
            'EUR' => self::PDDP_THRESHOLD_EUR,
        ];

        return $thresholds[$currencyCode] ?? null;
    }
}
