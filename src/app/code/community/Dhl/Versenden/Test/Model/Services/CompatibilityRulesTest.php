<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Services_CompatibilityRulesTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Assert that the pDDP rules expose a tooltip template for the above-limit
     * case so the popup can explain the disabled checkbox.
     *
     * @test
     */
    public function getPddpConfigProvidesLimitTooltipTemplate()
    {
        $address = Mage::getModel('sales/order_address');
        $address->setAddressType(Mage_Sales_Model_Order_Address::TYPE_SHIPPING);
        $address->setCountryId('US');

        $order = Mage::getModel('sales/order');
        $order->setStoreId(Mage::app()->getStore()->getId());
        $order->setBaseGrandTotal(1000.0);
        $order->addAddress($address);

        /** @var Dhl_Versenden_Model_Services_CompatibilityRules $rules */
        $rules = Mage::getModel('dhl_versenden/services_compatibilityRules', ['order' => $order]);
        $pddpConfig = $rules->getRules()['pddp'];

        static::assertSame(
            'Not available for USA/Puerto Rico orders above %s %s',
            $pddpConfig['limitTooltipTemplate'],
        );
    }
}
