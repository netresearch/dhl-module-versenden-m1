<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Observer_CheckoutProgressTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();

        $this->setCurrentStore('store_two');

        Mage::app()->getLayout()->getUpdate()->resetHandles();

        $baseCurrencyCode = 'USD';
        $currency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
        Mage::app()->getStore()->setData('current_currency', $currency);
        Mage::app()->getStore()->setData('base_currency', $currency);

        $quote = Mage::getModel('sales/quote');
        $shippingAddress = Mage::getModel('sales/quote_address');
        $shippingAddress->setData(
            'dhl_versenden_info',
            '{
                "schemaVersion":"1.0",
                "services":{
                    "preferredDay":"2016-11-12",
                    "preferredLocation":"testt",
                    "preferredNeighbour":null
                }
            }'
        );
        $shippingAddress->setShippingMethod('flatrate_foo');
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
