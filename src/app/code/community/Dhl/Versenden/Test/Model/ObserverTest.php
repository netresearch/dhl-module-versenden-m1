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

/**
 * Dhl_Versenden_Test_Model_ObserverTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function registerAutoload()
    {
        $configMock = $this->getModelMock('dhl_versenden/config', ['isAutoloadEnabled']);
        $configMock
            ->expects($this->once())
            ->method('isAutoloadEnabled')
            ->willReturnOnConsecutiveCalls(true)
        ;
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $autoloaderMock = $this->getHelperMock('dhl_versenden/autoloader', ['register']);
        $autoloaderMock
            ->expects($this->once())
            ->method('register')
        ;
        $this->replaceByMock('helper', 'dhl_versenden/autoloader', $autoloaderMock);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->registerAutoload();
    }

    /**
     * @test
     */
    public function registerAutoloadOff()
    {
        $configMock = $this->getModelMock('dhl_versenden/config', ['isAutoloadEnabled']);
        $configMock
            ->expects($this->once())
            ->method('isAutoloadEnabled')
            ->willReturnOnConsecutiveCalls(false)
        ;
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $autoloaderMock = $this->getHelperMock('dhl_versenden/autoloader', ['register']);
        $autoloaderMock
            ->expects($this->never())
            ->method('register')
        ;
        $this->replaceByMock('helper', 'dhl_versenden/autoloader', $autoloaderMock);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->registerAutoload();
    }

    /**
     * @test
     * @loadFixture ../../ConfigTest/fixtures/ConfigTest
     */
    public function appendServices()
    {
        $this->setCurrentStore('store_two');

        $observer = new Varien_Event_Observer();
        $block = new Mage_Checkout_Block_Onepage_Shipping_Method_Available();
        $blockHtml = '<span>foo</span>';
        $transport = new Varien_Object();
        $transport->setHtml($blockHtml);

        $observer->setBlock($block);
        $observer->setTransport($transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->appendServices($observer);

        $this->assertStringStartsWith($blockHtml, $transport->getHtml());
        $this->assertContains('checkout-dhlversenden-services', $transport->getHtml());
    }
}
