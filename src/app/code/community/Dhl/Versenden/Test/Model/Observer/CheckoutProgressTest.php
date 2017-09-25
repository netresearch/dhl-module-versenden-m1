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
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Model_Observer_CheckoutTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Observer_CheckoutProgressTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();

        $this->setCurrentStore('store_two');
        Mage::app()->getLayout()->getUpdate()->resetHandles();

        $quote = Mage::getModel('sales/quote');
        $shippingAddress = Mage::getModel('sales/quote_address');
        $shippingAddress->setData(
            'dhl_versenden_info',
            '{
                "schemaVersion":"1.0",
                "services":{
                    "preferredDay":"2016-11-12",
                    "preferredTime":"12001400",
                    "preferredLocation":"testt",
                    "preferredNeighbour":null
                }
            }'
        );
        $quote->addShippingAddress($shippingAddress);

        $sessionMock = $this->getModelMock('checkout/session', array('init', 'getQuote'));
        $sessionMock
            ->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $this->replaceByMock('model', 'checkout/session', $sessionMock);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function displayServicesYes()
    {
        $observer  = new Varien_Event_Observer();
        $blockHtml = '<dd>Preferred Day</dd>';

        /** @var Mage_Checkout_Block_Onepage_Progress $block */
        $block = Mage::app()->getLayout()->createBlock('checkout/onepage_progress');

        $transport = new Varien_Object();
        $transport->setData('html', $blockHtml);

        $observer->setData('block', $block);
        $observer->setData('transport', $transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();

        // Case: correct block
        $block->getLayout()->getUpdate()->addHandle('checkout_onepage_progress_shipping_method');
        $observer->setData('block', $block);

        $dhlObserver->appendServicesToShippingMethod($observer);
        $this->assertContains('Preferred Day', $transport->getData('html'));
        $this->assertContains('12 - 14', $transport->getData('html'));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function displayServicesNo()
    {
        $observer  = new Varien_Event_Observer();
        $blockHtml = '<dd>Preferred Day</dd>';

        /** @var Mage_Checkout_Block_Onepage_Progress $block */
        $block = Mage::app()->getLayout()->createBlock('checkout/onepage_progress');

        $transport = new Varien_Object();
        $transport->setData('html', $blockHtml);

        $observer->setData('block', $block);
        $observer->setData('transport', $transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();

        // Case: wrong block
        $block->getLayout()->getUpdate()->addHandle('checkout_onepage_progress_shipping_address');
        $dhlObserver->appendServicesToShippingMethod($observer);
        $this->assertEquals($blockHtml, $transport->getData('html'));
    }
}
