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
use \Dhl\Versenden\Webservice\ResponseData;
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
     * Do not actually save anything during test runs.
     */
    protected function setUp()
    {
        parent::setUp();

        $transactionMock = $this->getModelMock('core/resource_transaction', array('save'));
        $this->replaceByMock('model', 'core/resource_transaction', $transactionMock);

        $historyMock = $this->getModelMock('sales/order_status_history', array('save'));
        $this->replaceByMock('model', 'sales/order_status_history', $historyMock);
    }

    /**
     * @return Dhl_Versenden_Model_Log
     */
    protected function getLogger()
    {
        $config = Mage::getModel('dhl_versenden/config');
        $logger = Mage::getModel('dhl_versenden/log', array('config' => $config));

        return $logger;
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function autoCreateStatusOk()
    {
        $statusText = 'ok';
        $statusMessage = 'No errors.';

        $status          = new ResponseData\Status\Item('0', '0', $statusText, $statusMessage);
        $labelCollection = new ResponseData\CreateShipment\LabelCollection();
        $labelArgs       = array($status, '10', '123123');

        $labelMock = $this->getMockBuilder(ResponseData\CreateShipment\Label::class)
            ->setConstructorArgs($labelArgs)
            ->setMethods(array('getAllLabels'))
            ->getMock();
        $labelMock
            ->expects($this->once())
            ->method('getAllLabels')
            ->willReturn('label_foo');


        $labelCollection->addItem($labelMock);
        $shipmentNumbers = array('10' => '10');
        $result = new ResponseData\CreateShipment($status, $labelCollection, $shipmentNumbers);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array('createShipmentOrder'));
        $gatewayMock
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($result);
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        /** @var Dhl_Versenden_Model_Shipping_Autocreate $autocreate */
        $autocreate = Mage::getModel('dhl_versenden/shipping_autocreate', array('logger' => $this->getLogger()));
        $createdLabelsCount = $autocreate->autoCreate($collection);
        $this->assertEquals(1, $createdLabelsCount);

        /** @var Mage_Sales_Model_Order $order */
        $order = $collection->getItemById(10);
        $shipmentCollection = $order->getShipmentsCollection();

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            $this->assertEquals('label_foo', $shipment->getShippingLabel());
        }
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function autoCreateStatusNotOk()
    {
        $statusText = 'Hard validation error.';
        $statusMessagePartOne = 'The foo is not bar.';
        $statusMessagePartTwo = 'The fox is not pink.';

        $statusMessage = array($statusMessagePartOne, $statusMessagePartTwo);

        $status          = new ResponseData\Status\Item('0', '1101', $statusText, $statusMessage);
        $labelCollection = new ResponseData\CreateShipment\LabelCollection();
        $labelArgs       = array($status, '10', '123123');

        $labelMock = $this->getMockBuilder(ResponseData\CreateShipment\Label::class)
            ->setConstructorArgs($labelArgs)
            ->setMethods(array('getAllLabels'))
            ->getMock();


        $labelCollection->addItem($labelMock);
        $shipmentNumbers = array('10' => '10');
        $result = new ResponseData\CreateShipment($status, $labelCollection, $shipmentNumbers);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array('createShipmentOrder'));
        $gatewayMock
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($result);
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        /** @var Dhl_Versenden_Model_Shipping_Autocreate $autocreate */
        $autocreate = Mage::getModel('dhl_versenden/shipping_autocreate', array('logger' => $this->getLogger()));
        $createdLabelsCount = $autocreate->autoCreate($collection);
        $this->assertEquals(0, $createdLabelsCount);

        /** @var Mage_Sales_Model_Order $order */
        $order = $collection->getItemById(10);
        $shipmentCollection = $order->getShipmentsCollection();
        $historyCollection = $order->getStatusHistoryCollection();

        $this->assertEmpty($shipmentCollection);
        /** @var Mage_Sales_Model_Order_Status_History $historyItem */
        foreach ($historyCollection as $historyItem) {
            $this->assertStringEndsWith(
                "$statusText $statusMessagePartOne $statusMessagePartTwo",
                $historyItem->getComment()
            );
        }
    }

    /**
     * @test
     */
    public function autoCreateStatusCollectionCountZero()
    {
        /** @var Dhl_Versenden_Model_Resource_Autocreate_Collection $collection */
        $collection         = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        /** @var Dhl_Versenden_Model_Shipping_Autocreate $autocreate */
        $autocreate = Mage::getModel('dhl_versenden/shipping_autocreate', array('logger' => $this->getLogger()));
        $createdLabelsCount = $autocreate->autoCreate($collection);

        $this->assertEquals(0, $createdLabelsCount);
    }
}
