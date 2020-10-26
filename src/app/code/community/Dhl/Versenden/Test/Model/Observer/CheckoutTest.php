<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service as ServiceBlock;

class Dhl_Versenden_Test_Model_Observer_CheckoutTest extends EcomDev_PHPUnit_Test_Case
{
    public function setUp()
    {
        $mockQuote = $this->getModelMockBuilder('sales/quote')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSession = $this->getModelMockBuilder('customer/session')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSession->method('getQuote')->willReturn($mockQuote);
        $this->replaceByMock('singleton', 'customer/session', $mockSession);

        parent::setUp();
    }

    /**
     * @param string $serviceBlockHtml
     * @param bool $render
     * @return void
     */
    private function initServiceBlock($serviceBlockHtml, $render = true)
    {
        $blockType = 'dhl_versenden/checkout_onepage_shipping_method_service';

        /** @var ServiceBlock|PHPUnit_Framework_MockObject_MockObject $blockMock */
        $blockMock = $this->getBlockMock($blockType, array('renderView'), false, array(), '', false);
        $blockMock->setTemplate('dhl_versenden/checkout/shipping_services.phtml');

        $blockMock
            ->expects($render ? $this->any() : $this->never())
            ->method('renderView')
            ->willReturn($serviceBlockHtml);

        $this->replaceByMock('block', $blockType, $blockMock);
    }

    /**
     * Make sure that the Wunschpaket services block is appended if
     * - carrier is enabled for checkout
     * - shipping origin is DE
     * - the current block to be rendered is the shipping methods block
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function appendWunschpaketServices()
    {
        // config for store_three: carrier enabled, shipping origin DE
        $this->setCurrentStore('store_three');

        $shippingMethodsBlockHtml = '<span>foo</span>';
        $serviceBlockHtml = '<span class="checkout-dhlversenden-services"/>';

        $this->initServiceBlock($serviceBlockHtml);

        $block = new Mage_Checkout_Block_Onepage_Shipping_Method_Available();
        $transport = new Varien_Object();
        $transport->setHtml($shippingMethodsBlockHtml);

        $observer = new Varien_Event_Observer();
        $observer->setBlock($block);
        $observer->setTransport($transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();
        $dhlObserver->appendServices($observer);

        $this->assertStringStartsWith($shippingMethodsBlockHtml, $transport->getHtml());
        $this->assertStringEndsWith($serviceBlockHtml, $transport->getHtml());
    }

    /**
     * Make sure that the Wunschpaket services block is *not* appended if
     * - carrier is *not* enabled for checkout
     * - shipping origin is DE
     * - the current block to be rendered is the shipping methods block
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function doNotAppendWunschpaketServicesIfCarrierIsDisabledForCheckout()
    {
        // config for store_one: carrier not enabled, shipping origin DE
        $this->setCurrentStore('store_one');

        $shippingMethodsBlockHtml = '<span>foo</span>';
        $serviceBlockHtml = '<span class="checkout-dhlversenden-services"/>';

        $this->initServiceBlock($serviceBlockHtml, false);

        $block = new Mage_Checkout_Block_Onepage_Shipping_Method_Available();
        $transport = new Varien_Object();
        $transport->setHtml($shippingMethodsBlockHtml);

        $observer = new Varien_Event_Observer();
        $observer->setBlock($block);
        $observer->setTransport($transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();
        $dhlObserver->appendServices($observer);

        $this->assertStringStartsWith($shippingMethodsBlockHtml, $transport->getHtml());
        $this->assertStringEndsNotWith($serviceBlockHtml, $transport->getHtml());
    }

    /**
     * Make sure that the Wunschpaket services block is *not* appended if
     * - carrier is enabled for checkout
     * - shipping origin is not DE
     * - the current block to be rendered is the shipping methods block
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function doNotAppendWunschpaketServicesIfOriginIsAt()
    {
        // config for store_one: carrier enabled, shipping origin AT
        $this->setCurrentStore('store_two');

        $shippingMethodsBlockHtml = '<span>foo</span>';
        $serviceBlockHtml = '<span class="checkout-dhlversenden-services"/>';

        $this->initServiceBlock($serviceBlockHtml, false);

        $block = new Mage_Checkout_Block_Onepage_Shipping_Method_Available();
        $transport = new Varien_Object();
        $transport->setHtml($shippingMethodsBlockHtml);

        $observer = new Varien_Event_Observer();
        $observer->setBlock($block);
        $observer->setTransport($transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();
        $dhlObserver->appendServices($observer);

        $this->assertStringStartsWith($shippingMethodsBlockHtml, $transport->getHtml());
        $this->assertStringEndsNotWith($serviceBlockHtml, $transport->getHtml());
    }
    /**
     * Make sure that the Wunschpaket services block is *not* appended if
     * - carrier is enabled for checkout
     * - shipping origin is DE
     * - the current block to be rendered is *not* the shipping methods block
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function doNotAppendWunschpaketToArbitraryBlock()
    {
        // config for store_three: carrier enabled, shipping origin DE
        $this->setCurrentStore('store_three');

        $shippingMethodsAdditionalBlockHtml = '<span>bar</span>';
        $serviceBlockHtml = '<span class="checkout-dhlversenden-services"/>';

        $this->initServiceBlock($serviceBlockHtml, false);

        // rendering another block here
        $block = new Mage_Checkout_Block_Onepage_Shipping_Method_Additional();
        $transport = new Varien_Object();
        $transport->setHtml($shippingMethodsAdditionalBlockHtml);

        $observer = new Varien_Event_Observer();
        $observer->setBlock($block);
        $observer->setTransport($transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();
        $dhlObserver->appendServices($observer);

        $this->assertStringStartsWith($shippingMethodsAdditionalBlockHtml, $transport->getHtml());
        $this->assertStringEndsNotWith($serviceBlockHtml, $transport->getHtml());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ObserverTest
     */
    public function saveShippingSettings()
    {
        $this->setCurrentStore('store_two');

        // SERVICE DEFINITION
        $parcelAnnouncement = 'parcelAnnouncement';

        // two settings, only one actually enabled
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->setMethods(array('getPost'))
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getPost')
            ->withConsecutive($this->equalTo('shipment_service'), $this->equalTo('service_setting'))
            ->willReturnMap(
                array(
                    array('shipment_service', array(), array(
                        $parcelAnnouncement => $parcelAnnouncement
                    )),
                    array('service_setting', array(), array(
                    ))
                )
            );

        /** @var Varien_Event_Observer|PHPUnit_Framework_MockObject_MockObject $observerMock */
        $observerMock = $this->getMockBuilder('Varien_Event_Observer')
            ->setMethods(array('getRequest'))
            ->getMock();
        $observerMock
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        // ADDRESS DEFINITION
        $addressCompany = 'Dhl Foo Company';

        // use guest quote where shipping address has no email
        $quote = Mage::getModel('sales/quote')->load(210);
        $quote->getShippingAddress()->setCompany($addressCompany);
        $observerMock->setQuote($quote);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();
        $dhlObserver->saveShippingSettings($observerMock);

        /** @var \Dhl\Versenden\Bcs\Api\Info $versendenInfo */
        $versendenInfo = $quote->getShippingAddress()->getData('dhl_versenden_info');
        $this->assertInstanceOf('\Dhl\Versenden\Bcs\Api\Info', $versendenInfo);
        $this->assertTrue($versendenInfo->getServices()->parcelAnnouncement);
        $this->assertNull($versendenInfo->getServices()->preferredNeighbour);
        $this->assertEquals($addressCompany, $versendenInfo->getReceiver()->name2);
        $this->assertNotEmpty($versendenInfo->getReceiver()->email);
    }

    /**
     * Assert early return (wrong shipping method).
     *
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function saveNoShippingSettings()
    {
        $this->setCurrentStore('store_one');
        $quote = Mage::getModel('sales/quote')->load(100);

        /** @var Varien_Event_Observer|PHPUnit_Framework_MockObject_MockObject $observerMock */
        $observerMock = $this->getMockBuilder('Varien_Event_Observer')
            ->setMethods(array('getRequest'))
            ->getMock();
        $observerMock
            ->expects($this->never())
            ->method('getRequest');
        $observerMock->setQuote($quote);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Services();
        $dhlObserver->saveShippingSettings($observerMock);
    }

    /**
     * @test
     */
    public function updateCarrier()
    {
        $fooCarrier = 'foo';
        $dhlCarrier = Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE;
        $method     = 'bar';

        $observer = new Varien_Event_Observer();
        $order = new Varien_Object();
        $order->setShippingMethod("{$fooCarrier}_{$method}");
        $observer->setOrder($order);

        $configMock = $this->getModelMock('dhl_versenden/config_shipment', array('canProcessMethod'));
        $configMock
            ->expects($this->any())
            ->method('canProcessMethod')
            ->willReturnOnConsecutiveCalls(false, true);
        $this->replaceByMock('model', 'dhl_versenden/config_shipment', $configMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer();

        $dhlObserver->updateCarrier($observer);
        $this->assertEquals("{$fooCarrier}_{$method}", $observer->getOrder()->getShippingMethod());

        $dhlObserver->updateCarrier($observer);
        $this->assertEquals("{$dhlCarrier}_{$method}", $observer->getOrder()->getShippingMethod());
    }
}
