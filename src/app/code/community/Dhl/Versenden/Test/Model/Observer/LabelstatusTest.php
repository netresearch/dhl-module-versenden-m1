<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Test label status observer functionality.
 *
 * This test class verifies that label status tracking works correctly
 * during shipment label creation and deletion via REST API.
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 */
class Dhl_Versenden_Test_Model_Observer_LabelstatusTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test that label status is updated to PROCESSED after successful label creation.
     *
     * This test verifies the event-driven label status tracking:
     * 1. Event 'dhl_versenden_create_shipment_order_after' is dispatched
     * 2. Observer setLabelCreatedStatus() executes
     * 3. Label status is updated from PENDING to PROCESSED in database
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function labelStatusUpdatedOnSuccessfulCreation()
    {
        // Load order from fixture
        $order = Mage::getModel('sales/order')->load(1);
        static::assertNotNull($order->getId(), 'Order fixture not loaded');

        // Create initial PENDING status
        $labelStatus = Mage::getModel('dhl_versenden/label_status');
        $labelStatus->setOrderId($order->getId());
        $labelStatus->setStatusCode(Dhl_Versenden_Model_Label_Status::CODE_PENDING);
        $labelStatus->save();

        // Verify initial state
        $statusBefore = Mage::getModel('dhl_versenden/label_status')
            ->load($order->getId(), 'order_id');
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PENDING,
            $statusBefore->getStatusCode(),
            'Initial status should be PENDING',
        );

        // Create mock shipment request
        $shipmentRequest = new Mage_Shipping_Model_Shipment_Request();
        $shipmentRequest->setOrderShipment($order->getShipmentsCollection()->getFirstItem());

        // Mock REST SDK response (simulate successful label creation)
        $mockShipment = $this->getMockBuilder('stdClass')
            ->addMethods(['getShipmentNumber', 'getLabels'])
            ->getMock();
        $mockShipment->method('getShipmentNumber')->willReturn('12345678901');
        $mockShipment->method('getLabels')->willReturn(['label-data-base64']);

        // Dispatch the event (simulates what REST code should do)
        $eventData = [
            'request_data' => [$shipmentRequest],
            'result' => [$mockShipment],
        ];
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_after', $eventData);

        // Verify status was updated to PROCESSED
        $statusAfter = Mage::getModel('dhl_versenden/label_status')
            ->load($order->getId(), 'order_id');
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PROCESSED,
            $statusAfter->getStatusCode(),
            'Status should be updated to PROCESSED after successful label creation',
        );
    }

    /**
     * Test that label status is updated to FAILED when label creation fails.
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function labelStatusUpdatedOnFailedCreation()
    {
        // Load order from fixture
        $order = Mage::getModel('sales/order')->load(1);

        // Create initial PENDING status
        $labelStatus = Mage::getModel('dhl_versenden/label_status');
        $labelStatus->setOrderId($order->getId());
        $labelStatus->setStatusCode(Dhl_Versenden_Model_Label_Status::CODE_PENDING);
        $labelStatus->save();

        // Create mock shipment request
        $shipmentRequest = new Mage_Shipping_Model_Shipment_Request();
        $shipmentRequest->setOrderShipment($order->getShipmentsCollection()->getFirstItem());

        // Simulate failed creation (null result)
        $eventData = [
            'request_data' => [$shipmentRequest],
            'result' => null,
        ];
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_after', $eventData);

        // Verify status was updated to FAILED
        $statusAfter = Mage::getModel('dhl_versenden/label_status')
            ->load($order->getId(), 'order_id');
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_FAILED,
            $statusAfter->getStatusCode(),
            'Status should be updated to FAILED when label creation fails',
        );
    }

    /**
     * Test that label status is reset to PENDING after successful label deletion.
     *
     * This test verifies:
     * 1. Event 'dhl_versenden_delete_shipment_order_after' is dispatched
     * 2. Observer setLabelDeletedStatus() executes
     * 3. Label status is updated from PROCESSED back to PENDING
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function labelStatusResetOnSuccessfulDeletion()
    {
        // Load order from fixture
        $order = Mage::getModel('sales/order')->load(1);
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        // Create track
        $track = Mage::getModel('sales/order_shipment_track');
        $track->setShipment($shipment);
        $track->setOrderId($order->getId());
        $track->setTrackNumber('12345678901');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);
        $track->save();

        // Create initial PROCESSED status
        $labelStatus = Mage::getModel('dhl_versenden/label_status');
        $labelStatus->setOrderId($order->getId());
        $labelStatus->setStatusCode(Dhl_Versenden_Model_Label_Status::CODE_PROCESSED);
        $labelStatus->save();

        // Verify initial state
        $statusBefore = Mage::getModel('dhl_versenden/label_status')
            ->load($order->getId(), 'order_id');
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PROCESSED,
            $statusBefore->getStatusCode(),
            'Initial status should be PROCESSED',
        );

        // Mock REST SDK deletion response (successful)
        $mockDeletion = $this->getMockBuilder('stdClass')
            ->addMethods(['isError'])
            ->getMock();
        $mockDeletion->method('isError')->willReturn(false);

        // Dispatch the event (simulates what REST code should do)
        $eventData = [
            'request_data' => ['12345678901'],
            'result' => $this->getMockDeletionResult($mockDeletion, '12345678901'),
        ];
        Mage::dispatchEvent('dhl_versenden_delete_shipment_order_after', $eventData);

        // Verify status was reset to PENDING
        $statusAfter = Mage::getModel('dhl_versenden/label_status')
            ->load($order->getId(), 'order_id');
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PENDING,
            $statusAfter->getStatusCode(),
            'Status should be reset to PENDING after successful label deletion',
        );
    }

    /**
     * Create mock deletion result with getDeletedItems() method.
     *
     * @param object $mockDeletion Mock deletion item
     * @param string $trackNumber Track number
     * @return object
     */
    protected function getMockDeletionResult($mockDeletion, $trackNumber)
    {
        $mockDeletedItems = $this->getMockBuilder('stdClass')
            ->addMethods(['getItem'])
            ->getMock();
        $mockDeletedItems->method('getItem')
            ->with($trackNumber)
            ->willReturn($mockDeletion);

        $mockResult = $this->getMockBuilder('stdClass')
            ->addMethods(['getDeletedItems'])
            ->getMock();
        $mockResult->method('getDeletedItems')->willReturn($mockDeletedItems);

        return $mockResult;
    }

    /**
     * Test that initLabelStatus creates PENDING status for DHL orders.
     *
     * @test
     */
    public function initLabelStatusCreatesPendingForDhlOrder()
    {
        // Create an order with DHL shipping method
        $order = Mage::getModel('sales/order');
        $order->setShippingMethod('dhlversenden_flatrate');
        $order->setId(999); // Fake order ID

        $observer = new Varien_Event_Observer();
        $observer->setData('order', $order);

        // Mock the label status model to capture the save
        $statusMock = $this->getModelMock('dhl_versenden/label_status', ['save']);
        $statusMock->expects(static::once())
            ->method('save');
        $this->replaceByMock('model', 'dhl_versenden/label_status', $statusMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Labelstatus();
        $dhlObserver->initLabelStatus($observer);

        static::assertEquals(999, $statusMock->getOrderId());
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PENDING,
            $statusMock->getStatusCode(),
        );
    }

    /**
     * Test that initLabelStatus does nothing for non-DHL orders.
     *
     * @test
     */
    public function initLabelStatusIgnoresNonDhlOrder()
    {
        // Create an order with non-DHL shipping method
        $order = Mage::getModel('sales/order');
        $order->setShippingMethod('flatrate_flatrate');
        $order->setId(888);

        $observer = new Varien_Event_Observer();
        $observer->setData('order', $order);

        // Mock the label status model - should NOT be called
        $statusMock = $this->getModelMock('dhl_versenden/label_status', ['save']);
        $statusMock->expects(static::never())
            ->method('save');
        $this->replaceByMock('model', 'dhl_versenden/label_status', $statusMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Labelstatus();
        $dhlObserver->initLabelStatus($observer);
    }

    /**
     * Test that addStatusToOrderGridCollection joins the status table.
     *
     * @test
     */
    public function addStatusToOrderGridCollectionJoinsStatusTable()
    {
        // Create a collection mock with real select object behavior
        $selectMock = $this->getMockBuilder(Varien_Db_Select::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPart', 'joinLeft'])
            ->getMock();

        $selectMock->method('getPart')
            ->with('from')
            ->willReturn([]); // No 'status' key means join hasn't happened

        $selectMock->expects(static::once())
            ->method('joinLeft');

        $collectionMock = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Grid_Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSelect', 'getTable'])
            ->getMock();

        $collectionMock->method('getSelect')
            ->willReturn($selectMock);
        $collectionMock->method('getTable')
            ->with('dhl_versenden/label_status')
            ->willReturn('dhl_versenden_label_status');

        $observer = new Varien_Event_Observer();
        $observer->setData('order_grid_collection', $collectionMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Labelstatus();
        $dhlObserver->addStatusToOrderGridCollection($observer);
    }

    /**
     * Test that addStatusToOrderGridCollection skips join if already joined.
     *
     * @test
     */
    public function addStatusToOrderGridCollectionSkipsIfAlreadyJoined()
    {
        $selectMock = $this->getMockBuilder(Varien_Db_Select::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPart', 'joinLeft'])
            ->getMock();

        // Status is already in the 'from' part
        $selectMock->method('getPart')
            ->with('from')
            ->willReturn(['status' => ['some-join-data']]);

        // joinLeft should NOT be called since status is already joined
        $selectMock->expects(static::never())
            ->method('joinLeft');

        $collectionMock = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Grid_Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSelect'])
            ->getMock();

        $collectionMock->method('getSelect')
            ->willReturn($selectMock);

        $observer = new Varien_Event_Observer();
        $observer->setData('order_grid_collection', $collectionMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Labelstatus();
        $dhlObserver->addStatusToOrderGridCollection($observer);
    }

    /**
     * Test that addColumnToGrid adds status column to sales order grid.
     *
     * @test
     */
    public function addColumnToGridAddsStatusColumn()
    {
        // Create admin session mock to allow adminhtml block creation
        $sessionMock = $this->getModelMock('admin/session', ['init', 'isAllowed']);
        $sessionMock->method('isAllowed')->willReturn(true);
        $this->replaceByMock('singleton', 'admin/session', $sessionMock);

        // Mock the grid block
        $layoutMock = $this->getMockBuilder(Mage_Core_Model_Layout::class)
            ->setMethods(['createBlock'])
            ->getMock();

        $filterBlockMock = $this->getMockBuilder(Dhl_Versenden_Block_Adminhtml_Sales_Order_Grid::class)
            ->disableOriginalConstructor()
            ->getMock();

        $layoutMock->method('createBlock')
            ->with('dhl_versenden/adminhtml_sales_order_grid')
            ->willReturn($filterBlockMock);

        $gridBlockMock = $this->getMockBuilder(Mage_Adminhtml_Block_Sales_Order_Grid::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLayout', 'addColumnAfter', '__'])
            ->getMock();

        $gridBlockMock->method('getLayout')
            ->willReturn($layoutMock);
        $gridBlockMock->method('__')
            ->willReturnArgument(0);
        $gridBlockMock->expects(static::once())
            ->method('addColumnAfter')
            ->with(
                'status_code',
                static::isType('array'),
                'status',
            );

        $event = new Varien_Event();
        $event->setData('block', $gridBlockMock);

        $observer = new Varien_Event_Observer();
        $observer->setEvent($event);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Labelstatus();
        $dhlObserver->addColumnToGrid($observer);
    }

    /**
     * Test that addColumnToGrid ignores non-sales-order-grid blocks.
     *
     * @test
     */
    public function addColumnToGridIgnoresOtherBlocks()
    {
        $blockMock = Mage::app()->getLayout()->createBlock('core/text');

        $event = new Varien_Event();
        $event->setData('block', $blockMock);

        $observer = new Varien_Event_Observer();
        $observer->setEvent($event);

        // Should not throw any errors or exceptions
        $dhlObserver = new Dhl_Versenden_Model_Observer_Labelstatus();
        $dhlObserver->addColumnToGrid($observer);

        // If we get here without errors, the test passes
        static::assertTrue(true);
    }

    /**
     * Test filterStatus returns early when no value provided.
     *
     * @test
     */
    public function filterStatusReturnsEarlyWhenNoValue()
    {
        $filterMock = $this->getMockBuilder(Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();
        $filterMock->method('getValue')->willReturn(null);

        $columnMock = $this->getMockBuilder(Mage_Adminhtml_Block_Widget_Grid_Column::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilter'])
            ->getMock();
        $columnMock->method('getFilter')->willReturn($filterMock);

        $collectionMock = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Grid_Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['join', 'addFieldToFilter'])
            ->getMock();

        // join and addFieldToFilter should NOT be called
        $collectionMock->expects(static::never())->method('join');
        $collectionMock->expects(static::never())->method('addFieldToFilter');

        $dhlObserver = new Dhl_Versenden_Model_Observer_Labelstatus();
        $result = $dhlObserver->filterStatus($collectionMock, $columnMock);

        static::assertSame($dhlObserver, $result);
    }
}
