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
use \Dhl\Versenden\Bcs\Api\Shipment\Service;
/**
 * Dhl_Versenden_Test_Model_Observer_CheckoutTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Observer_CheckoutTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function appendServices()
    {
        $this->setCurrentStore('store_two');
        $serviceBlockHtml = 'checkout-dhlversenden-services';

        $blockType = 'dhl_versenden/checkout_onepage_shipping_method_service';
        $blockMock = $this->getBlockMock(
            $blockType,
            array('renderView'),
            false,
            array(array('template' => 'dhl_versenden/checkout/shipping_services.phtml'))
        );
        $blockMock
            ->expects($this->any())
            ->method('renderView')
            ->willReturn($serviceBlockHtml);
        $this->replaceByMock('block', $blockType, $blockMock);

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
        $this->assertStringEndsWith($serviceBlockHtml, $transport->getHtml());
    }

    /**
     * Assert early return.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function appendNoServices()
    {
        $this->setCurrentStore('store_two');

        $observerMock = $this->getMockBuilder(Varien_Event_Observer::class)
            ->setMethods(array('getTransport'))
            ->getMock();
        $observerMock
            ->expects($this->never())
            ->method('getTransport');

        $block = new Mage_Core_Block_Text();
        $observerMock->setBlock($block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->appendServices($observerMock);
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
        $requestMock = $this->getMockBuilder(Mage_Core_Controller_Request_Http::class)
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

        $observerMock = $this->getMockBuilder(Varien_Event_Observer::class)
            ->setMethods(array('getRequest'))
            ->getMock();
        $observerMock
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        // ADDRESS DEFINITION
        $addressCompany = 'Dhl Foo Company';

        $quote = Mage::getModel('sales/quote')->load(200);
        $quote->getShippingAddress()->setCompany($addressCompany);
        $observerMock->setQuote($quote);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->saveShippingSettings($observerMock);

        /** @var \Dhl\Versenden\Bcs\Api\Info $versendenInfo */
        $versendenInfo = $quote->getShippingAddress()->getData('dhl_versenden_info');
        $this->assertInstanceOf(\Dhl\Versenden\Bcs\Api\Info::class, $versendenInfo);
        $this->assertTrue($versendenInfo->getServices()->parcelAnnouncement);
        $this->assertNull($versendenInfo->getServices()->preferredNeighbour);
        $this->assertEquals($addressCompany, $versendenInfo->getReceiver()->name2);
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

        $observerMock = $this->getMockBuilder(Varien_Event_Observer::class)
            ->setMethods(array('getRequest'))
            ->getMock();
        $observerMock
            ->expects($this->never())
            ->method('getRequest');
        $observerMock->setQuote($quote);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
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
