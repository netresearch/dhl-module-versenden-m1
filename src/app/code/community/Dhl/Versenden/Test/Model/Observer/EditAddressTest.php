<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Observer_EditAddressTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();
        // load layout
        Mage::app()->getLayout()->createBlock('dhl_versenden/adminhtml_sales_order_address_form', 'dhl_versenden_form');

        // mock session usage
        $blockMock = $this->getBlockMock('adminhtml/sales_order_address', array('getBackUrl'));
        $this->replaceByMock('block', 'adminhtml/sales_order_address', $blockMock);
    }

    /**
     * @return \Dhl\Versenden\Bcs\Api\Info
     */
    protected function prepareVersendenInfo()
    {
        $streetName = 'Street Name';
        $streetNumber = '127';

        $versendenInfo = new \Dhl\Versenden\Bcs\Api\Info();
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

        $this->assertSame($formBlock, $block->getChild('form'));
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

        $this->assertSame($formBlock, $block->getChild('form'));
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

        $this->assertSame($formBlock, $block->getChild('form'));
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

        $this->assertSame($formBlock, $block->getChild('form'));
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

        $this->assertSame($formBlock, $block->getChild('form'));
    }

    /**
     * @test
     * @registry order_address
     * @loadFixture Model_ObserverTest
     */
    public function replaceAddressFormOk()
    {
        $versendenInfo = $this->prepareVersendenInfo();
        $order = Mage::getModel('sales/order')->load(10);
        $address = Mage::getModel('sales/order_address');
        $address->setOrder($order);
        $address->setData('dhl_versenden_info', $versendenInfo);
        $order->setShippingAddress($address);
        Mage::register('order_address', $address);

        $block = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_address');
        $formBlock = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_address_form');
        $block->setChild('form', $formBlock);

        $observer = new Varien_Event_Observer();
        $observer->setData('block', $block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->replaceAddressForm($observer);

        $this->assertNotSame($formBlock, $block->getChild('form'));
        $this->assertInstanceOf(
            Dhl_Versenden_Block_Adminhtml_Sales_Order_Address_Form::class,
            $block->getChild('form')
        );
    }
}
