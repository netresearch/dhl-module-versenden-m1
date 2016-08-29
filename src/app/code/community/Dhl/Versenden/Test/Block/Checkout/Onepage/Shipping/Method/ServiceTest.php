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
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Shipment\Service;
/**
 * Dhl_Versenden_Test_Block_Checkout_Onepage_Shipping_Method_ServiceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Block_Checkout_Onepage_Shipping_Method_ServiceTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServices()
    {
        $this->setCurrentStore('store_two');

        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setCountryId('DE');
        $quote = new Mage_Sales_Model_Quote();
        $quote->setStoreId(Mage::app()->getStore()->getId());
        $quote->setShippingAddress($shippingAddress);

        $blockType = 'dhl_versenden/checkout_onepage_shipping_method_service';
        $blockMock = $this->getBlockMock($blockType, array('getQuote'));
        $blockMock
            ->expects($this->exactly(2))
            ->method('getQuote')
            ->willReturn($quote);
        $this->replaceByMock('block', $blockType, $blockMock);

        $serviceOne = new Service\BulkyGoods('', true, false);
        $serviceTwo = new Service\PreferredLocation('', true, false, '');
        $collection = new Service\Collection([
            $serviceOne, $serviceTwo
        ]);

        $configMock = $this->getModelMock('dhl_versenden/config_service', ['getEnabledServices']);
        $configMock
            ->expects($this->once())
            ->method('getEnabledServices')
            ->willReturn($collection);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $configMock);

        $block = Mage::app()->getLayout()->createBlock('dhl_versenden/checkout_onepage_shipping_method_service');

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
        $this->setCurrentStore('store_two');

        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setCountryId('DE');
        $quote = new Mage_Sales_Model_Quote();
        $quote->setStoreId(Mage::app()->getStore()->getId());
        $quote->setShippingAddress($shippingAddress);

        $blockType = 'dhl_versenden/checkout_onepage_shipping_method_service';
        $blockMock = $this->getBlockMock($blockType, array('getQuote'));
        $blockMock
            ->expects($this->exactly(1))
            ->method('getQuote')
            ->willReturn($quote);
        $this->replaceByMock('block', $blockType, $blockMock);

        $block = Mage::app()->getLayout()->createBlock('dhl_versenden/checkout_onepage_shipping_method_service');
        $json = $block->getDhlMethods();
        $methods = Mage::helper('core/data')->jsonDecode($json);
        $this->assertInternalType('array', $methods);
        $this->assertCount(2, $methods);
        $this->assertContains('flatrate_flatrate', $methods);
        $this->assertContains('tablerate_bestway', $methods);
    }
}
