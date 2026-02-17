<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Test_Block_Checkout_Onepage_Shipping_Method_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    public const BLOCK_ALIAS = 'dhl_versenden/checkout_onepage_shipping_method_service';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setCurrentStore('store_one');
        $shippingAddress = Mage::getModel('sales/quote_address');
        $shippingAddress->setCountryId('DE');

        $quote = Mage::getModel('sales/quote');
        $quote->setStoreId(Mage::app()->getStore()->getId());
        $quote->setShippingAddress($shippingAddress);

        // Mock services processor to avoid quote->getStore() null pointer issue
        $processorMock = $this->getModelMock('dhl_versenden/services_processor', ['processServices']);
        $processorMock
            ->expects(static::any())
            ->method('processServices')
            ->willReturnArgument(0); // Return services unchanged
        $this->replaceByMock('model', 'dhl_versenden/services_processor', $processorMock);

        $blockMock = $this->getBlockMock(self::BLOCK_ALIAS, ['getQuote']);
        $blockMock
            ->expects(static::any())
            ->method('getQuote')
            ->willReturn($quote);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $blockMock);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function getDhlMethods()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $json    = $block->getDhlMethods();
        $methods = Mage::helper('core/data')->jsonDecode($json);
        static::assertIsArray($methods);
        static::assertCount(2, $methods);
        static::assertContains('flatrate_flatrate', $methods);
        static::assertContains('tablerate_bestway', $methods);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceHint()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        static::assertNotEmpty($block->getServiceHint(Service\PreferredDay::CODE));
        static::assertNotEmpty($block->getServiceHint(Service\PreferredLocation::CODE));
        static::assertNotEmpty($block->getServiceHint(Service\PreferredNeighbour::CODE));
        static::assertNotEmpty($block->getServiceHint(Service\ParcelAnnouncement::CODE));
        static::assertNotEmpty($block->getServiceHint(Service\NoNeighbourDelivery::CODE));
        static::assertNotEmpty($block->getServiceHint(Service\GoGreenPlus::CODE));
        static::assertNotEmpty($block->getServiceHint(Service\ClosestDropPoint::CODE));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isShippingAddressDHLLocation()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        // No Location Data is used (normal order)
        $isAddressLocation = $block->isShippingAddressDHLLocation();
        static::assertEquals(false, $isAddressLocation);

        // Got DHL Location fields from Shipping Address
        $block->getQuote()->getShippingAddress()->setData('dhl_station_type', 'packstation');
        $isAddressLocation = $block->isShippingAddressDHLLocation();
        static::assertEquals(true, $isAddressLocation);

        // Got Info Object but with no location data
        $versendenInfo = new \Dhl\Versenden\ParcelDe\Info();
        $block->getQuote()->getShippingAddress()->setData('dhl_station_type', null);
        $block->getQuote()->getShippingAddress()->setData('dhl_versenden_info', $versendenInfo);
        $isAddressLocation = $block->isShippingAddressDHLLocation();
        static::assertEquals(false, $isAddressLocation);

        // Got Info Object with location data
        $versendenInfo->getReceiver()->getPackstation()->packstationNumber = 1234567;
        $block->getQuote()->getShippingAddress()->setData('dhl_versenden_info', $versendenInfo);
        $isAddressLocation = $block->isShippingAddressDHLLocation();
        static::assertEquals(true, $isAddressLocation);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceFeeText()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        static::assertNotEmpty($block->getServiceFeeText(Service\PreferredDay::CODE));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isOnePageCheckout()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        // No One Page Checkout
        Mage::app()->getLayout()->getUpdate()->resetHandles();
        Mage::app()->getLayout()->getUpdate()->addHandle('checkout_third_party');
        $isOnePageCheckout = $block->isOnePageCheckout();
        static::assertEquals(false, $isOnePageCheckout);

        // One Page Checkout
        Mage::app()->getLayout()->getUpdate()->resetHandles();
        Mage::app()->getLayout()->getUpdate()->addHandle('checkout_onepage');
        $isOnePageCheckout = $block->isOnePageCheckout();
        static::assertEquals(true, $isOnePageCheckout);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceFeeTextReturnsEmptyForUnknownService()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        // Unknown service code should return empty string
        static::assertEmpty($block->getServiceFeeText('unknownService'));
        static::assertEmpty($block->getServiceFeeText(Service\PreferredLocation::CODE));
    }

    // =========================================================================
    // DHLGKP-XXX: CDP (Closest Drop Point) Checkout Block Tests
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceHintForClosestDropPoint()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        static::assertNotEmpty($block->getServiceHint(Service\ClosestDropPoint::CODE));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceHeadlineForClosestDropPoint()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        static::assertNotEmpty($block->getServiceHeadline(Service\ClosestDropPoint::CODE));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceHeadlineForNoNeighbourDelivery()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        static::assertNotEmpty($block->getServiceHeadline(Service\NoNeighbourDelivery::CODE));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceHeadlineForGoGreenPlus()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        static::assertNotEmpty($block->getServiceHeadline(Service\GoGreenPlus::CODE));
    }

    // =========================================================================
    // DHLGKP-XXX: NoNeighbourDelivery and GoGreen Surcharge Block Tests
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceFeeTextForNoNeighbourDelivery()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        // NoNeighbourDelivery fee is 0.29 in fixture, so fee text should be non-empty
        static::assertNotEmpty($block->getServiceFeeText(Service\NoNeighbourDelivery::CODE));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceFeeTextForGoGreen()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        // GoGreen fee is 0.50 in fixture, so fee text should be non-empty
        static::assertNotEmpty($block->getServiceFeeText(Service\GoGreenPlus::CODE));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceFeeTextForClosestDropPoint()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        // CDP fee is 1.50 in fixture, so fee text should be non-empty
        static::assertNotEmpty($block->getServiceFeeText(Service\ClosestDropPoint::CODE));
    }
}
