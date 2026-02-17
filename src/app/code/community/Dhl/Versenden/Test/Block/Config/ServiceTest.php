<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

/**
 * Test Block Config Service functionality.
 *
 * Tests the service configuration block used in checkout to display
 * selected DHL services and associated fees.
 */
class Dhl_Versenden_Test_Block_Config_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Create block with mocked dependencies.
     *
     * @return Dhl_Versenden_Block_Config_Service
     */
    protected function createBlock()
    {
        // Mock checkout session to prevent constructor issues
        $sessionMock = $this->getModelMock('checkout/session', ['init', 'getQuote']);
        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStoreId'])
            ->getMock();

        $shippingAddressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getShippingMethod', 'getData'])
            ->getMock();
        $shippingAddressMock->method('getShippingMethod')->willReturn('flatrate_flatrate');
        $shippingAddressMock->method('getData')->willReturn(null);

        $quoteMock->method('getShippingAddress')->willReturn($shippingAddressMock);
        $quoteMock->method('getStoreId')->willReturn(1);
        $sessionMock->method('getQuote')->willReturn($quoteMock);
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        // Mock config to return false for canProcessMethod
        $configMock = $this->getModelMock('dhl_versenden/config_shipment', ['canProcessMethod']);
        $configMock->method('canProcessMethod')->willReturn(false);
        $this->replaceByMock('model', 'dhl_versenden/config_shipment', $configMock);

        // Set up store currency so Mage_Core_Helper_Data::currency() works
        $currency = Mage::getModel('directory/currency')->load('USD');
        Mage::app()->getStore()->setData('current_currency', $currency);
        Mage::app()->getStore()->setData('base_currency', $currency);

        return new Dhl_Versenden_Block_Config_Service();
    }

    /**
     * Test renderName returns correct service names.
     *
     * @test
     */
    public function renderNameReturnsCorrectServiceNames()
    {
        $block = $this->createBlock();

        static::assertEquals(Service\PreferredDay::LABEL, $block->renderName(Service\PreferredDay::CODE));
        static::assertEquals(Service\PreferredLocation::LABEL, $block->renderName(Service\PreferredLocation::CODE));
        static::assertEquals(Service\PreferredNeighbour::LABEL, $block->renderName(Service\PreferredNeighbour::CODE));
        static::assertEquals(Service\ParcelAnnouncement::LABEL, $block->renderName(Service\ParcelAnnouncement::CODE));
        static::assertEquals(Service\NoNeighbourDelivery::LABEL, $block->renderName(Service\NoNeighbourDelivery::CODE));
        static::assertEquals(Service\GoGreenPlus::LABEL, $block->renderName(Service\GoGreenPlus::CODE));
        static::assertEquals(Service\ClosestDropPoint::LABEL, $block->renderName(Service\ClosestDropPoint::CODE));
    }

    /**
     * Test renderDate returns formatted date string.
     *
     * @test
     */
    public function renderDateReturnsFormattedDateString()
    {
        // Set fixed timezone to ensure consistent results across all environments
        $originalTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');

        try {
            $block = $this->createBlock();
            $result = $block->renderDate('2025-12-25');

            // Should return a date with day, month, year separated by . or /
            static::assertMatchesRegularExpression('/\d{2}[.\/]\d{2}[.\/]\d{4}/', $result);
            // Should contain year 2025
            static::assertStringContainsString('2025', $result);
            // Should contain day 25 (UTC timezone ensures no date shift)
            static::assertStringContainsString('25', $result, 'Date should contain day 25');
        } finally {
            date_default_timezone_set($originalTimezone);
        }
    }

    /**
     * Test isAnyServiceSelected returns true when services selected.
     *
     * @test
     */
    public function isAnyServiceSelectedReturnsTrueWhenServicesExist()
    {
        $block = $this->createBlock();

        // Set services with at least one value
        $services = ['preferredDay' => '2025-12-25', 'preferredLocation' => null];
        $block->setData('services', $services);

        static::assertTrue($block->isAnyServiceSelected());
    }

    /**
     * Test isAnyServiceSelected returns false when no services selected.
     *
     * @test
     */
    public function isAnyServiceSelectedReturnsFalseWhenNoServices()
    {
        $block = $this->createBlock();

        // Set empty services
        $block->setData('services', []);

        static::assertFalse($block->isAnyServiceSelected());
    }

    /**
     * Test isAnyServiceSelected returns false when all services null.
     *
     * @test
     */
    public function isAnyServiceSelectedReturnsFalseWhenAllNull()
    {
        $block = $this->createBlock();

        // Set services all null/empty
        $services = ['preferredDay' => null, 'preferredLocation' => '', 'preferredNeighbour' => false];
        $block->setData('services', $services);

        static::assertFalse($block->isAnyServiceSelected());
    }

    /**
     * Test isAnyServiceSelected handles service object with toArray.
     *
     * @test
     */
    public function isAnyServiceSelectedHandlesServiceObject()
    {
        $block = $this->createBlock();

        // Create services object
        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $services->preferredDay = '2025-12-25';
        $block->setData('services', $services);

        static::assertTrue($block->isAnyServiceSelected());
    }

    /**
     * Test isAnyServiceSelected handles empty service object.
     *
     * @test
     */
    public function isAnyServiceSelectedHandlesEmptyServiceObject()
    {
        $block = $this->createBlock();

        // Create empty services object
        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $block->setData('services', $services);

        static::assertFalse($block->isAnyServiceSelected());
    }

    /**
     * Test renderFeeText returns empty string when no surcharge services selected.
     *
     * @test
     */
    public function renderFeeTextReturnsEmptyWhenNoSurchargeServicesSelected()
    {
        $block = $this->createBlock();

        // Set services without any surcharge service
        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $services->preferredLocation = 'Front door';
        $block->setData('services', $services);

        static::assertEquals('', $block->renderFeeText());
    }

    /**
     * Test renderFeeText returns empty when all fees are zero.
     *
     * @test
     */
    public function renderFeeTextReturnsEmptyWhenAllFeesZero()
    {
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getPrefDayFee', 'getNoNeighbourDeliveryFee', 'getGoGreenFee', 'getCdpFee']
        );
        $serviceConfigMock->method('getPrefDayFee')->willReturn(0);
        $serviceConfigMock->method('getNoNeighbourDeliveryFee')->willReturn(0);
        $serviceConfigMock->method('getGoGreenFee')->willReturn(0);
        $serviceConfigMock->method('getCdpFee')->willReturn(0);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $block = $this->createBlock();

        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $services->preferredDay = '2025-12-25';
        $services->goGreen = true;
        $block->setData('services', $services);

        static::assertEquals('', $block->renderFeeText());
    }

    /**
     * Test renderFeeText returns fee text for GoGreen surcharge.
     *
     * @test
     */
    public function renderFeeTextReturnsTextForGoGreenFee()
    {
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getPrefDayFee', 'getNoNeighbourDeliveryFee', 'getGoGreenFee', 'getCdpFee']
        );
        $serviceConfigMock->method('getPrefDayFee')->willReturn(0);
        $serviceConfigMock->method('getNoNeighbourDeliveryFee')->willReturn(0);
        $serviceConfigMock->method('getGoGreenFee')->willReturn(1.50);
        $serviceConfigMock->method('getCdpFee')->willReturn(0);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $block = $this->createBlock();

        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $services->goGreen = true;
        $block->setData('services', $services);

        $result = $block->renderFeeText();
        static::assertNotEmpty($result);
        static::assertStringContainsString('1.50', $result);
        // Singular: one surcharge service selected
        static::assertStringContainsString('your preferred delivery option', $result);
        static::assertStringNotContainsString('your preferred delivery options', $result);
    }

    /**
     * Test renderFeeText returns fee text for NoNeighbourDelivery surcharge.
     *
     * @test
     */
    public function renderFeeTextReturnsTextForNoNeighbourDeliveryFee()
    {
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getPrefDayFee', 'getNoNeighbourDeliveryFee', 'getGoGreenFee', 'getCdpFee']
        );
        $serviceConfigMock->method('getPrefDayFee')->willReturn(0);
        $serviceConfigMock->method('getNoNeighbourDeliveryFee')->willReturn(2.00);
        $serviceConfigMock->method('getGoGreenFee')->willReturn(0);
        $serviceConfigMock->method('getCdpFee')->willReturn(0);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $block = $this->createBlock();

        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $services->noNeighbourDelivery = true;
        $block->setData('services', $services);

        $result = $block->renderFeeText();
        static::assertNotEmpty($result);
        static::assertStringContainsString('2.00', $result);
        // Singular: one surcharge service selected
        static::assertStringContainsString('your preferred delivery option', $result);
        static::assertStringNotContainsString('your preferred delivery options', $result);
    }

    /**
     * Test renderFeeText returns fee text for ClosestDropPoint surcharge.
     *
     * @test
     */
    public function renderFeeTextReturnsTextForClosestDropPointFee()
    {
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getPrefDayFee', 'getNoNeighbourDeliveryFee', 'getGoGreenFee', 'getCdpFee']
        );
        $serviceConfigMock->method('getPrefDayFee')->willReturn(0);
        $serviceConfigMock->method('getNoNeighbourDeliveryFee')->willReturn(0);
        $serviceConfigMock->method('getGoGreenFee')->willReturn(0);
        $serviceConfigMock->method('getCdpFee')->willReturn(3.00);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $block = $this->createBlock();

        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $services->closestDropPoint = true;
        $block->setData('services', $services);

        $result = $block->renderFeeText();
        static::assertNotEmpty($result);
        static::assertStringContainsString('3.00', $result);
        // Singular: one surcharge service selected
        static::assertStringContainsString('your preferred delivery option', $result);
        static::assertStringNotContainsString('your preferred delivery options', $result);
    }

    /**
     * Test renderFeeText returns combined text with summed fee for multiple services.
     *
     * @test
     */
    public function renderFeeTextReturnsCombinedTextForMultipleServices()
    {
        $serviceConfigMock = $this->getModelMock(
            'dhl_versenden/config_service',
            ['getPrefDayFee', 'getNoNeighbourDeliveryFee', 'getGoGreenFee', 'getCdpFee']
        );
        $serviceConfigMock->method('getPrefDayFee')->willReturn(1.20);
        $serviceConfigMock->method('getNoNeighbourDeliveryFee')->willReturn(0);
        $serviceConfigMock->method('getGoGreenFee')->willReturn(0.80);
        $serviceConfigMock->method('getCdpFee')->willReturn(0);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $block = $this->createBlock();

        $services = new \Dhl\Versenden\ParcelDe\Info\Services();
        $services->preferredDay = '2025-12-25';
        $services->goGreen = true;
        $block->setData('services', $services);

        $result = $block->renderFeeText();
        static::assertNotEmpty($result);
        static::assertStringContainsString('2.00', $result);
        // Plural: multiple surcharge services selected
        static::assertStringContainsString('your preferred delivery options', $result);
    }

    /**
     * Test getServices returns services from data.
     *
     * @test
     */
    public function getServicesReturnsServicesData()
    {
        $block = $this->createBlock();

        $services = ['preferredDay' => '2025-12-25'];
        $block->setData('services', $services);

        static::assertEquals($services, $block->getServices());
    }

    /**
     * Test getServices returns empty array when no services set in constructor.
     *
     * @test
     */
    public function getServicesReturnsEmptyArrayWhenCannotProcess()
    {
        $block = $this->createBlock();

        // When canProcessMethod returns false, services should be empty array
        static::assertEquals([], $block->getServices());
    }
}
