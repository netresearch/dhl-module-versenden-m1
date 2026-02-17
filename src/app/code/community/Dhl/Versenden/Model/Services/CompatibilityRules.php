<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;
use Dhl\Versenden\ParcelDe\Product;

/**
 * Aggregates service compatibility rules for client-side enforcement in the packaging popup.
 *
 * Composes domain classes (ProductServiceMatrix, service constants) with order context
 * to produce a complete rules structure for JavaScript consumption.
 */
class Dhl_Versenden_Model_Services_CompatibilityRules
{
    /** @var Mage_Sales_Model_Order */
    protected $order;

    /** @var int */
    protected $storeId;

    /** @var Dhl_Versenden_Model_Config_Service */
    protected $serviceConfig;

    /**
     * @param mixed[] $params Expected keys: 'order' (Mage_Sales_Model_Order)
     */
    public function __construct(array $params = [])
    {
        if (isset($params['order'])) {
            $this->order = $params['order'];
            $this->storeId = $this->order->getStoreId();
        }

        $this->serviceConfig = Mage::getModel('dhl_versenden/config_service');
    }

    /**
     * Build the complete rules structure for client-side compatibility enforcement.
     *
     * @return array
     */
    public function getRules()
    {
        return [
            'productServiceMatrix' => $this->getProductServiceMatrix(),
            'pddp' => $this->getPddpConfig(),
            'serviceRules' => $this->getServiceRules(),
            'productRadioOptions' => $this->getProductRadioOptions(),
        ];
    }

    /**
     * Canonical product-to-service mapping for enable/disable toggling.
     *
     * @return array<string, string[]>
     */
    protected function getProductServiceMatrix()
    {
        $matrix = new Service\ProductServiceMatrix();
        return $matrix->getMatrix();
    }

    /**
     * PDDP auto-enable configuration for US shipments.
     *
     * JS uses this to pre-check the pDDP checkbox when order value is under threshold.
     *
     * @return array
     */
    protected function getPddpConfig()
    {
        return [
            'recipientCountry' => $this->order->getShippingAddress()->getCountryId(),
            'orderValue' => (float) $this->order->getBaseGrandTotal(),
            'currency' => Mage::app()->getStore($this->storeId)->getBaseCurrencyCode(),
            'thresholdEur' => Dhl_Versenden_Model_Webservice_Builder_Service::PDDP_THRESHOLD_EUR,
            'thresholdUsd' => Dhl_Versenden_Model_Webservice_Builder_Service::PDDP_THRESHOLD_USD,
            'tooltipTemplate' => Mage::helper('dhl_versenden/data')->__(
                'Auto-enabled for US orders under %s %s',
            ),
        ];
    }

    /**
     * Service-to-service mutual exclusivity rules.
     *
     * Each rule: when master checkbox is checked, subject service gets disabled.
     *
     * @return array[]
     */
    protected function getServiceRules()
    {
        return [
            [
                'master' => Service\PreferredNeighbour::CODE,
                'subject' => Service\NoNeighbourDelivery::CODE,
                'action' => 'disable',
            ],
            [
                'master' => Service\NoNeighbourDelivery::CODE,
                'subject' => Service\PreferredNeighbour::CODE,
                'action' => 'disable',
            ],
        ];
    }

    /**
     * Product-specific radio option restrictions.
     *
     * Restricts DeliveryType radio options based on product and destination:
     * - Warenpost International: Economy+Premium only (CDP not supported)
     * - Weltpaket: Economy+Premium only when destination is not CDP-eligible
     *
     * @return array<string, array<string, string[]>>
     */
    protected function getProductRadioOptions()
    {
        $recipientCountry = $this->order->getShippingAddress()->getCountryId();
        $isCdpEligible = in_array($recipientCountry, Service\DeliveryType::CDP_ELIGIBLE_COUNTRIES, true);

        $noCdpOptions = [
            Service\DeliveryType::ECONOMY,
            Service\DeliveryType::PREMIUM,
        ];

        $options = [
            Product::CODE_WARENPOST_INTERNATIONAL => [
                Service\DeliveryType::CODE => $noCdpOptions,
            ],
        ];

        if (!$isCdpEligible) {
            $options[Product::CODE_WELTPAKET] = [
                Service\DeliveryType::CODE => $noCdpOptions,
            ];
        }

        return $options;
    }
}
