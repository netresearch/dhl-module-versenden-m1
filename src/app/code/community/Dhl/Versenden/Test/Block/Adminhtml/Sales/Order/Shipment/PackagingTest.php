<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_PackagingTest extends EcomDev_PHPUnit_Test_Case
{
    public const BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

    /**
     * Mock getShipment registry access
     * @see Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging::getShipment()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('DE');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);

        $blockMock = $this->getBlockMock(self::BLOCK_ALIAS, ['getShipment']);
        $blockMock
            ->expects(static::any())
            ->method('getShipment')
            ->willReturn($shipment);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $blockMock);
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function getStoreUnit()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $block->getShipment()->setStoreId(1);
        static::assertEquals('G', $block->getStoreUnit());

        $block->getShipment()->setStoreId(2);
        static::assertEquals('KG', $block->getStoreUnit());
    }

    /**
     * @test
     */
    public function getWeightUnits()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $units = ['foo', 'bar'];
        $carrierMock = $this->getModelMock('dhl_versenden/shipping_carrier_versenden', ['getCode']);
        $carrierMock
            ->expects(static::once())
            ->method('getCode')
            ->with(static::equalTo('unit_of_measure'))
            ->willReturn($units);
        $this->replaceByMock('singleton', 'dhl_versenden/shipping_carrier_versenden', $carrierMock);

        static::assertSame($units, $block->getWeightUnits());
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function displayCustomsValue()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $eu  = false;
        $row = true;

        $helperMock = $this->getHelperMock('dhl_versenden/data', ['isCollectCustomsData']);
        $helperMock
            ->expects(static::exactly(2))
            ->method('isCollectCustomsData')
            ->willReturnOnConsecutiveCalls($eu, $row);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);

        static::assertSame($eu, $block->displayCustomsValue());
        static::assertSame($row, $block->displayCustomsValue());
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function getCompatibilityRulesJsonReturnsValidStructure()
    {
        // Mock service config for POR email
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getParcelOutletNotificationEmail'],
        );
        $serviceConfigMock
            ->expects(static::any())
            ->method('getParcelOutletNotificationEmail')
            ->willReturn('test@example.com');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $json = $block->getCompatibilityRulesJson();
        static::assertIsString($json);

        $rules = json_decode($json, true);
        static::assertIsArray($rules, 'JSON must decode to array');

        // Top-level keys
        static::assertArrayHasKey('productServiceMatrix', $rules);
        static::assertArrayHasKey('pddp', $rules);

        // productServiceMatrix contains all products with service arrays
        $matrix = $rules['productServiceMatrix'];
        static::assertArrayHasKey('V01PAK', $matrix);
        static::assertArrayHasKey('V62KP', $matrix);
        static::assertIsArray($matrix['V01PAK']);
        static::assertContains('additionalInsurance', $matrix['V01PAK']);
        static::assertNotContains('additionalInsurance', $matrix['V62KP']);

        // pddp has expected fields
        $pddp = $rules['pddp'];
        static::assertArrayHasKey('recipientCountry', $pddp);
        static::assertArrayHasKey('orderValue', $pddp);
        static::assertArrayHasKey('currency', $pddp);
        static::assertArrayHasKey('thresholdEur', $pddp);
        static::assertArrayHasKey('thresholdUsd', $pddp);
        static::assertEquals('DE', $pddp['recipientCountry']);

        // serviceRules contains mutual exclusivity rules
        static::assertArrayHasKey('serviceRules', $rules);
        $serviceRules = $rules['serviceRules'];
        static::assertIsArray($serviceRules);
        static::assertNotEmpty($serviceRules, 'serviceRules must contain at least one rule');
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function serviceRulesContainNeighbourMutualExclusion()
    {
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getParcelOutletNotificationEmail'],
        );
        $serviceConfigMock
            ->expects(static::any())
            ->method('getParcelOutletNotificationEmail')
            ->willReturn('test@example.com');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $rules = json_decode($block->getCompatibilityRulesJson(), true);
        $serviceRules = $rules['serviceRules'];

        // Find the preferredNeighbour → noNeighbourDelivery rule
        $foundForward = false;
        $foundReverse = false;
        foreach ($serviceRules as $rule) {
            if ($rule['master'] === 'preferredNeighbour' && $rule['subject'] === 'noNeighbourDelivery') {
                $foundForward = true;
                static::assertEquals('disable', $rule['action']);
            }
            if ($rule['master'] === 'noNeighbourDelivery' && $rule['subject'] === 'preferredNeighbour') {
                $foundReverse = true;
                static::assertEquals('disable', $rule['action']);
            }
        }

        static::assertTrue($foundForward, 'Must have preferredNeighbour → noNeighbourDelivery disable rule');
        static::assertTrue($foundReverse, 'Must have noNeighbourDelivery → preferredNeighbour disable rule');
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function productRadioOptionsRestrictsCdpForNonCdpCountry()
    {
        // setUp sets recipient to DE, which is not CDP-eligible
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getParcelOutletNotificationEmail'],
        );
        $serviceConfigMock
            ->expects(static::any())
            ->method('getParcelOutletNotificationEmail')
            ->willReturn('test@example.com');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $rules = json_decode($block->getCompatibilityRulesJson(), true);

        static::assertArrayHasKey('productRadioOptions', $rules);
        $radioOptions = $rules['productRadioOptions'];

        // V66WPI must restrict DeliveryType to Economy+Premium (no CDP)
        static::assertArrayHasKey('V66WPI', $radioOptions);
        static::assertArrayHasKey('deliveryType', $radioOptions['V66WPI']);
        $allowedOptions = $radioOptions['V66WPI']['deliveryType'];
        static::assertContains('ECONOMY', $allowedOptions);
        static::assertContains('PREMIUM', $allowedOptions);
        static::assertNotContains('CDP', $allowedOptions);

        // V53WPAK (Weltpaket) must also restrict CDP for non-CDP-eligible country
        static::assertArrayHasKey('V53WPAK', $radioOptions);
        static::assertArrayHasKey('deliveryType', $radioOptions['V53WPAK']);
        $weltpaketOptions = $radioOptions['V53WPAK']['deliveryType'];
        static::assertContains('ECONOMY', $weltpaketOptions);
        static::assertContains('PREMIUM', $weltpaketOptions);
        static::assertNotContains('CDP', $weltpaketOptions);
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function productRadioOptionsAllowsCdpForCdpEligibleCountry()
    {
        // Override shipping address to CDP-eligible country
        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('FR');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);

        $blockMock = $this->getBlockMock(self::BLOCK_ALIAS, ['getShipment']);
        $blockMock
            ->expects(static::any())
            ->method('getShipment')
            ->willReturn($shipment);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $blockMock);

        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getParcelOutletNotificationEmail'],
        );
        $serviceConfigMock
            ->expects(static::any())
            ->method('getParcelOutletNotificationEmail')
            ->willReturn('test@example.com');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $rules = json_decode($block->getCompatibilityRulesJson(), true);
        $radioOptions = $rules['productRadioOptions'];

        // V66WPI must still restrict CDP (never supported for Warenpost)
        static::assertArrayHasKey('V66WPI', $radioOptions);
        static::assertNotContains('CDP', $radioOptions['V66WPI']['deliveryType']);

        // V53WPAK should NOT have restrictions for CDP-eligible country
        static::assertArrayNotHasKey('V53WPAK', $radioOptions);
    }
}
