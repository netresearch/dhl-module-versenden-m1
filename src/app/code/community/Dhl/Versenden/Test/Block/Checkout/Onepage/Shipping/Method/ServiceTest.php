<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Bcs\Api\Shipment\Service;

/**
 * Dhl_Versenden_Test_Block_Checkout_Onepage_Shipping_Method_ServiceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Block_Checkout_Onepage_Shipping_Method_ServiceTest
    extends EcomDev_PHPUnit_Test_Case
{
    const BLOCK_ALIAS = 'dhl_versenden/checkout_onepage_shipping_method_service';

    protected function setUp()
    {
        parent::setUp();
        $this->setCurrentStore('store_one');
        $shippingAddress = Mage::getModel('sales/quote_address');
        $shippingAddress->setCountryId('DE');

        $quote = Mage::getModel('sales/quote');
        $quote->setStoreId(Mage::app()->getStore()->getId());
        $quote->setShippingAddress($shippingAddress);

        $blockMock = $this->getBlockMock(self::BLOCK_ALIAS, array('getQuote'));
        $blockMock
            ->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $blockMock);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServices()
    {
        $serviceOne = new Service\BulkyGoods('', true, false);
        $serviceTwo = new Service\PreferredNeighbour('', true, false, 'testneighbour');
        $services   = array($serviceOne, $serviceTwo);
        $collection = new Service\Collection($services);

        $configMock = $this->getModelMock('dhl_versenden/config_service', array('getEnabledServices'));
        $configMock
            ->expects($this->once())
            ->method('getEnabledServices')
            ->willReturn($collection);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $configMock);

        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $frontendServices = $block->getServices();
        $this->assertInstanceOf(Service\Collection::class, $frontendServices);
        $this->assertCount(1, $frontendServices);
        $this->assertContains($serviceTwo, $frontendServices);
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
        $this->assertInternalType('array', $methods);
        $this->assertCount(2, $methods);
        $this->assertContains('flatrate_flatrate', $methods);
        $this->assertContains('tablerate_bestway', $methods);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceHint()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $this->assertNotEmpty($block->getServiceHint(Service\PreferredDay::CODE));
        $this->assertNotEmpty($block->getServiceHint(Service\PreferredTime::CODE));
        $this->assertNotEmpty($block->getServiceHint(Service\PreferredLocation::CODE));
        $this->assertNotEmpty($block->getServiceHint(Service\PreferredNeighbour::CODE));
        $this->assertEmpty($block->getServiceHint(Service\ParcelAnnouncement::CODE));
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
        $this->assertEquals(false, $isAddressLocation);

        // Got DHL Location fields from Shipping Address
        $block->getQuote()->getShippingAddress()->setData('dhl_station_type', 'packstation');
        $isAddressLocation = $block->isShippingAddressDHLLocation();
        $this->assertEquals(true, $isAddressLocation);

        // Got Info Object but with no location data
        $versendenInfo = new \Dhl\Versenden\Bcs\Api\Info();
        $block->getQuote()->getShippingAddress()->setData('dhl_station_type', null);
        $block->getQuote()->getShippingAddress()->setData('dhl_versenden_info', $versendenInfo);
        $isAddressLocation = $block->isShippingAddressDHLLocation();
        $this->assertEquals(false, $isAddressLocation);

        // Got Info Object with location data
        $versendenInfo->getReceiver()->getPackstation()->packstationNumber = 1234567;
        $block->getQuote()->getShippingAddress()->setData('dhl_versenden_info', $versendenInfo);
        $isAddressLocation = $block->isShippingAddressDHLLocation();
        $this->assertEquals(true, $isAddressLocation);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServiceFeeText()
    {
        /** @var Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $this->assertNotEmpty($block->getServiceFeeText(Service\PreferredDay::CODE));
        $this->assertNotEmpty($block->getServiceHint(Service\PreferredTime::CODE));
    }
}
