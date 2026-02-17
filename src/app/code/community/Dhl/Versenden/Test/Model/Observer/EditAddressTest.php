<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Observer_EditAddressTest extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock admin session - required for adminhtml block instantiation
        // Without this, createBlock('adminhtml/sales_order_address') returns false
        // This test was intermittently failing on PHP 8.3 due to non-deterministic test execution order
        // When other tests (like FormTest) ran first, their session mocks persisted and this test passed
        // When this test ran before any session-mocking test, admin blocks failed to instantiate
        $quoteSessionMock = $this->getModelMock('adminhtml/session_quote', ['init', 'getStoreId']);
        $quoteSessionMock
            ->expects(static::any())
            ->method('getStoreId')
            ->willReturn('default');
        $this->replaceByMock('singleton', 'adminhtml/session_quote', $quoteSessionMock);

        $coreSessionMock = $this->getModelMock('core/session', ['init']);
        $this->replaceByMock('singleton', 'core/session', $coreSessionMock);
    }

    /**
     * @return \Dhl\Versenden\ParcelDe\Info
     */
    protected function prepareVersendenInfo()
    {
        $streetName = 'Street Name';
        $streetNumber = '127';

        $versendenInfo = new \Dhl\Versenden\ParcelDe\Info();
        $versendenInfo->getReceiver()->streetName = $streetName;
        $versendenInfo->getReceiver()->streetNumber = $streetNumber;

        return $versendenInfo;
    }

    /**
     * @test
     * @registry order_address
     * @loadFixture Model_ObserverTest
     */
    public function replaceAddressFormWrongBlock()
    {
        $block = Mage::app()->getLayout()->createBlock('core/text');
        $formBlock = Mage::app()->getLayout()->createBlock('core/flush');
        $block->setChild('form', $formBlock);

        $observer = new Varien_Event_Observer();
        $observer->setData('block', $block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->replaceAddressForm($observer);

        static::assertSame($formBlock, $block->getChild('form'));
    }

    /**
     * @test
     * @registry order_address
     * @loadFixture Model_ObserverTest
     */
    public function replaceAddressFormWrongAddress()
    {
        $address = Mage::getModel('sales/order_address');
        $address->setAddressType(Mage_Customer_Model_Address_Abstract::TYPE_BILLING);
        Mage::register('order_address', $address);

        $block = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_address');
        $formBlock = Mage::app()->getLayout()->createBlock('core/flush');
        $block->setChild('form', $formBlock);

        $observer = new Varien_Event_Observer();
        $observer->setData('block', $block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->replaceAddressForm($observer);

        static::assertSame($formBlock, $block->getChild('form'));
    }

    /**
     * @test
     * @registry order_address
     * @loadFixture Model_ObserverTest
     */
    public function replaceAddressFormWrongCarrier()
    {
        $order = Mage::getModel('sales/order')->load(17);
        $address = Mage::getModel('sales/order_address');
        $address->setOrder($order);
        $order->setShippingAddress($address);
        Mage::register('order_address', $address);

        $block = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_address');
        $formBlock = Mage::app()->getLayout()->createBlock('core/flush');
        $block->setChild('form', $formBlock);

        $observer = new Varien_Event_Observer();
        $observer->setData('block', $block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->replaceAddressForm($observer);

        static::assertSame($formBlock, $block->getChild('form'));
    }

    /**
     * @test
     * @registry order_address
     * @loadFixture Model_ObserverTest
     */
    public function replaceAddressFormWrongInfoType()
    {
        $versendenInfo = new Varien_Object();
        $order = Mage::getModel('sales/order')->load(10);
        $address = Mage::getModel('sales/order_address');
        $address->setOrder($order);
        $address->setData('dhl_versenden_info', $versendenInfo);
        $order->setShippingAddress($address);
        Mage::register('order_address', $address);

        $block = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_address');
        $formBlock = Mage::app()->getLayout()->createBlock('core/flush');
        $block->setChild('form', $formBlock);

        $observer = new Varien_Event_Observer();
        $observer->setData('block', $block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->replaceAddressForm($observer);

        static::assertSame($formBlock, $block->getChild('form'));
    }

    /**
     * @test
     * @registry order_address
     * @loadFixture Model_ObserverTest
     */
    public function replaceAddressFormWrongFormType()
    {
        $versendenInfo = $this->prepareVersendenInfo();
        $order = Mage::getModel('sales/order')->load(10);
        $address = Mage::getModel('sales/order_address');
        $address->setOrder($order);
        $address->setData('dhl_versenden_info', $versendenInfo);
        $order->setShippingAddress($address);
        Mage::register('order_address', $address);

        $block = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_address');
        $formBlock = Mage::app()->getLayout()->createBlock('core/flush');
        $block->setChild('form', $formBlock);

        $observer = new Varien_Event_Observer();
        $observer->setData('block', $block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->replaceAddressForm($observer);

        static::assertSame($formBlock, $block->getChild('form'));
    }
}
