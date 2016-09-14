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
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Model_Shipping_AutocreateTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Shipping_AutocreateTest extends EcomDev_PHPUnit_Test_Case
{


    /**
     * @test
     * @loadFixture Model_ShippingAutoCreateTest
     */
    public function autoCreate()
    {
        $status          = new \Dhl\Versenden\Webservice\ResponseData\Status(0, 'ok', 'no error');
        $labelCollection = new \Dhl\Versenden\Webservice\ResponseData\LabelCollection();
        $labelArgs       = array($status, '10', '123123' );

        $labelMock = $this->getMockBuilder(\Dhl\Versenden\Webservice\ResponseData\Label::class)
            ->setConstructorArgs($labelArgs)
            ->setMethods(['getAllLabels'])
            ->getMock();
        $labelMock
            ->expects($this->once())
            ->method('getAllLabels')
            ->willReturn('label_foo');


        $labelCollection->addItem($labelMock);
        $shipmentNumbers = array('10' => '10');
        $result = new \Dhl\Versenden\Webservice\ResponseData\CreateShipment($status, $labelCollection, $shipmentNumbers);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', ['createShipmentOrder']);
        $gatewayMock
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($result);
        $this->replaceByMock('model','dhl_versenden/webservice_gateway_soap', $gatewayMock );

        $collection           = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $shipmentRequestCount = Mage::getModel('dhl_versenden/shipping_autocreate')->autoCreate($collection);


        $this->assertEquals(1,$shipmentRequestCount);
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load(10);
        $shipmentCollection = $order->getShipmentsCollection();

        $this->assertEquals(1, count($shipmentCollection));
        $shipment = $shipmentCollection->getFirstItem();
        $this->assertEquals('label_foo', $shipment->getShippingLabel());

    }


    /**
     * @test
     * @loadFixture Model_ShippingAutoCreateTest
     */
    public function autoCreateStatusNotOk()
    {
        $status          = new \Dhl\Versenden\Webservice\ResponseData\Status('1101', 'error', 'error');
        $labelCollection = new \Dhl\Versenden\Webservice\ResponseData\LabelCollection();
        $labelArgs       = array($status, '10', '123123' );

        $labelMock = $this->getMockBuilder(\Dhl\Versenden\Webservice\ResponseData\Label::class)
            ->setConstructorArgs($labelArgs)
            ->setMethods(['getAllLabels'])
            ->getMock();


        $labelCollection->addItem($labelMock);
        $shipmentNumbers = array('10' => '10');
        $result = new \Dhl\Versenden\Webservice\ResponseData\CreateShipment($status, $labelCollection, $shipmentNumbers);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', ['createShipmentOrder']);
        $gatewayMock
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($result);
        $this->replaceByMock('model','dhl_versenden/webservice_gateway_soap', $gatewayMock );

        $collection           = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $shipmentRequestCount = Mage::getModel('dhl_versenden/shipping_autocreate')->autoCreate($collection);
        $order                = Mage::getModel('sales/order')->load(10);
        $historyCollection    = $order->getStatusHistoryCollection();
        $shipmentCollection   = $order->getShipmentsCollection();

        $this->assertEquals(1,$shipmentRequestCount);
        $this->assertEquals(0, count($shipmentCollection));
        $this->assertEquals(null, $shipmentCollection->getFirstItem()->getId());
        $this->assertEquals('(x) error error', $historyCollection->getFirstItem()->getComment());
    }


    /**
     * @test
     * @loadFixture Model_ShippingAutoCreateTest
     * @expectedException SoapFault
     */
    public function autoCreateStatusNoShipment()
    {
        $shipmentMock = $this->getModelMock('sales/order_shipment', ['getId']);
        $shipmentMock
            ->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $this->replaceByMock('model','sales/order_shipment', $shipmentMock );

        /** @var Dhl_Versenden_Model_Resource_Autocreate_Collection $collection */
        $collection           = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $shipmentRequestCount = Mage::getModel('dhl_versenden/shipping_autocreate')->autoCreate($collection);


    }


    /**
     * @test
     */
    public function autoCreateStatusCollectionCountZero()
    {

        /** @var Dhl_Versenden_Model_Resource_Autocreate_Collection $collection */
        $collection           = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $shipmentRequestCount = Mage::getModel('dhl_versenden/shipping_autocreate')->autoCreate($collection);

        $this->assertEquals(0,$shipmentRequestCount);
    }
}
