<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Controller_Adminhtml_Sales_Order_AutocreateControllerTest extends Dhl_Versenden_Test_Case_AdminController
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock logger to prevent actual logging during tests
        $writerMock = $this->getModelMock('dhl_versenden/logger_writer', ['log', 'logException']);
        $this->replaceByMock('singleton', 'dhl_versenden/logger_writer', $writerMock);
    }

    /**
     * Create a mock CreatedShipment object for successful API responses.
     *
     * @param string $shipmentNumber
     * @param string $labelData
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSuccessfulShipmentMock($shipmentNumber, $labelData = 'PDF_LABEL_DATA')
    {
        $mock = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Model\CreatedShipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShipmentNumber', 'getLabels'])
            ->getMock();

        $mock->expects(static::any())
            ->method('getShipmentNumber')
            ->willReturn($shipmentNumber);

        $mock->expects(static::any())
            ->method('getLabels')
            ->willReturn([$labelData]);

        return $mock;
    }

    /**
     * Test that empty collection shows error message.
     *
     * Uses order 17 from fixture which has non-DHL shipping method (xyz_foo).
     *
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function massCreateNoApplicableOrders()
    {
        $sessionMock = $this->getModelMock('adminhtml/session', ['addError']);
        $sessionMock
            ->expects(static::once())
            ->method('addError')
            ->with(Dhl_Versenden_Adminhtml_Sales_Order_AutocreateController::MESSAGE_NO_APPLICABLE_ORDERS);
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        // Order 17 has xyz_foo shipping method (non-DHL), so collection will be empty after filter
        $this->getRequest()->setPost(['order_ids' => [17]]);
        $this->dispatch('adminhtml/sales_order_autocreate/massCreateShipmentLabel');
        $this->assertRedirectTo('adminhtml/sales_order/index');
    }

    /**
     * Test successful label creation for single order.
     *
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function massCreateSingleOrderSuccess()
    {
        $shipmentNumber = '00340434161094015902';

        // Mock REST client - returns successful shipment
        $createdShipmentMock = $this->createSuccessfulShipmentMock($shipmentNumber);
        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', ['createShipments']);
        $clientMock->expects(static::once())
            ->method('createShipments')
            ->willReturn([$createdShipmentMock]);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Expect success message: "1 labels were created for 1 orders."
        $sessionMock = $this->getModelMock('adminhtml/session', ['addNotice']);
        $sessionMock
            ->expects(static::once())
            ->method('addNotice')
            ->with(static::stringContains('1 labels were created for 1 orders'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        // Dispatch with order 10 (DHL shipping, no existing shipment)
        $this->getRequest()->setPost(['order_ids' => [10]]);
        $this->dispatch('adminhtml/sales_order_autocreate/massCreateShipmentLabel');
        $this->assertRedirectTo('adminhtml/sales_order/index');

        // Verify shipment was created in database
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 10);
        static::assertEquals(1, $shipmentCollection->getSize(), 'Shipment should be created in DB');

        // Verify tracking number
        $shipment = $shipmentCollection->getFirstItem();
        $tracks = $shipment->getTracksCollection();
        static::assertEquals(1, $tracks->getSize(), 'Track should be created');
        static::assertEquals($shipmentNumber, $tracks->getFirstItem()->getNumber());
    }

    /**
     * Test partial success with some orders failing.
     *
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateMultiOrderTest
     */
    public function massCreatePartialFailure()
    {
        // Mock REST client - 3 orders: success, failure, success
        $shipment20 = $this->createSuccessfulShipmentMock('223344556677000');
        $shipment22 = $this->createSuccessfulShipmentMock('223344556677002');

        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', ['createShipments']);
        $clientMock->expects(static::once())
            ->method('createShipments')
            ->willReturn([
                $shipment20,  // Order 20 succeeds
                null,         // Order 21 fails
                $shipment22,  // Order 22 succeeds
            ]);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Expect notice message containing the failed order's increment ID
        $sessionMock = $this->getModelMock('adminhtml/session', ['addNotice']);
        $sessionMock
            ->expects(static::once())
            ->method('addNotice')
            ->with(static::stringContains('100000021'));  // Failed order increment ID
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        // Dispatch with all 3 orders
        $this->getRequest()->setPost(['order_ids' => [20, 21, 22]]);
        $this->dispatch('adminhtml/sales_order_autocreate/massCreateShipmentLabel');
        $this->assertRedirectTo('adminhtml/sales_order/index');

        // Verify correct orders have shipments
        $shipmentCollection20 = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 20);
        static::assertEquals(1, $shipmentCollection20->getSize(), 'Order 20 should have shipment');

        $shipmentCollection21 = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 21);
        static::assertEquals(0, $shipmentCollection21->getSize(), 'Order 21 should NOT have shipment');

        $shipmentCollection22 = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 22);
        static::assertEquals(1, $shipmentCollection22->getSize(), 'Order 22 should have shipment');
    }

    /**
     * Test exception handling during autocreate.
     *
     * The autocreate model catches ServiceException internally, but the controller
     * has a safety catch for any other Exception that might escape. We test this
     * by mocking the autocreate singleton to throw a generic exception.
     *
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function massCreateException()
    {
        $exceptionMessage = 'Unexpected error during autocreate';

        // Mock the autocreate model singleton to throw an exception
        $autocreateMock = $this->getModelMock(
            'dhl_versenden/shipping_autocreate',
            ['autoCreate'],
            false,
            [],
            '',
            false,
        );
        $autocreateMock
            ->expects(static::once())
            ->method('autoCreate')
            ->willThrowException(new Exception($exceptionMessage));
        $this->replaceByMock('singleton', 'dhl_versenden/shipping_autocreate', $autocreateMock);

        // Expect error message with exception text
        $sessionMock = $this->getModelMock('adminhtml/session', ['addError']);
        $sessionMock
            ->expects(static::once())
            ->method('addError')
            ->with($exceptionMessage);
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $this->getRequest()->setPost(['order_ids' => [10]]);
        $this->dispatch('adminhtml/sales_order_autocreate/massCreateShipmentLabel');
        $this->assertRedirectTo('adminhtml/sales_order/index');
    }
}
