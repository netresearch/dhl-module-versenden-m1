<?php

/**
 * See LICENSE.md for license details.
 *
 * Phase 3.6.3: Autocreate REST Migration Tests
 * Created: 2025-10-07
 *
 * Tests for bulk shipment creation using REST client instead of SOAP gateway.
 */

class Dhl_Versenden_Test_Model_Shipping_AutocreateRestTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @return Dhl_Versenden_Model_Log
     */
    protected function getLogger()
    {
        $config = Mage::getModel('dhl_versenden/config');
        $logger = Mage::getModel('dhl_versenden/log', ['config' => $config]);

        return $logger;
    }

    /**
     * Create a mock CreatedShipment object for successful API responses.
     *
     * @param string $shipmentNumber
     * @param string $labelData
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createSuccessfulShipmentMock($shipmentNumber, $labelData)
    {
        $mock = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Model\CreatedShipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShipmentNumber', 'getLabels'])
            ->getMock();

        $mock->expects(static::once())
            ->method('getShipmentNumber')
            ->willReturn($shipmentNumber);

        $mock->expects(static::once())
            ->method('getLabels')
            ->willReturn([$labelData]);

        return $mock;
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function autoCreateWithRestClientSuccess()
    {
        // Mock REST client to return successful shipment
        $shipmentNumber = '00340434161094015902';
        $labelData = 'PDF_LABEL_DATA_BASE64';

        // Mock CreatedShipment object
        $createdShipmentMock = $this->createSuccessfulShipmentMock($shipmentNumber, $labelData);

        // Mock REST client - returns array with CreatedShipment at index matching request
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['createShipments'],
        );

        $clientMock->expects(static::once())
            ->method('createShipments')
            ->willReturn([
                $createdShipmentMock,  // Order 10 succeeds
            ]);

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Get orders for autocreate
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        // Execute autocreate
        $autocreate = new Dhl_Versenden_Model_Shipping_Autocreate(['logger' => $this->getLogger()]);
        $createdLabelsCount = $autocreate->autoCreate($collection);

        // Assert 1 shipment created successfully
        static::assertEquals(1, $createdLabelsCount);

        // Verify DB state - shipment should be persisted
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 10);
        static::assertEquals(1, $shipmentCollection->getSize(), 'Shipment should be created in DB');

        $shipment = $shipmentCollection->getFirstItem();
        static::assertNotEmpty($shipment->getShippingLabel(), 'Shipping label should be saved');

        // Verify tracking number persisted
        $tracks = $shipment->getTracksCollection();
        static::assertEquals(1, $tracks->getSize(), 'Tracking number should be created');
        static::assertEquals('00340434161094015902', $tracks->getFirstItem()->getNumber(), 'Tracking number should match');
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function autoCreateWithRestClientFailure()
    {
        // Mock REST client - API returns null (failure)
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['createShipments'],
        );

        // API returns null for the shipment (failure)
        $clientMock->expects(static::once())
            ->method('createShipments')
            ->willReturn([
                null,  // Order 10 fails (null = API error)
            ]);

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Get orders
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        // Execute - API failure
        $autocreate = new Dhl_Versenden_Model_Shipping_Autocreate(['logger' => $this->getLogger()]);
        $createdLabelsCount = $autocreate->autoCreate($collection);

        // No shipments created (API failure)
        static::assertEquals(0, $createdLabelsCount);

        // Verify no shipment created in DB
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 10);
        static::assertEquals(0, $shipmentCollection->getSize(), 'No shipment should be created on API failure');

        // Verify error comment was saved to order status history
        $order = Mage::getModel('sales/order')->load(10);
        $foundError = false;
        foreach ($order->getStatusHistoryCollection() as $comment) {
            if (strpos($comment->getComment(), 'Shipment creation failed') !== false) {
                $foundError = true;
                break;
            }
        }
        static::assertTrue($foundError, 'Error comment should be saved to order status history');
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function autoCreateWithRestClientTotalFailure()
    {
        // Mock REST client to throw ServiceException (entire batch failed)
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['createShipments'],
        );

        $clientMock->expects(static::once())
            ->method('createShipments')
            ->willThrowException(
                new \Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException('API connection failed'),
            );

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Get orders
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        // Execute - should catch exception and return 0
        $autocreate = new Dhl_Versenden_Model_Shipping_Autocreate(['logger' => $this->getLogger()]);
        $createdLabelsCount = $autocreate->autoCreate($collection);

        // Total failure - 0 created
        static::assertEquals(0, $createdLabelsCount);

        // Verify no shipments were created
        /** @var Mage_Sales_Model_Order $order */
        $order = $collection->getItemById(10);
        $shipmentCollection = $order->getShipmentsCollection();

        static::assertEmpty($shipmentCollection);

        // Verify DB state - no shipments should exist
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 10);
        static::assertEquals(0, $shipmentCollection->getSize(), 'No shipment should exist in DB after total failure');
    }

    /**
     * @test
     */
    public function autoCreateWithEmptyCollection()
    {
        // Empty collection - should return 0 immediately without API call
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');

        $autocreate = new Dhl_Versenden_Model_Shipping_Autocreate(['logger' => $this->getLogger()]);
        $createdLabelsCount = $autocreate->autoCreate($collection);

        static::assertEquals(0, $createdLabelsCount);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function autoCreateWithEmptyResultHandling()
    {
        // Test defensive handling when API returns empty array
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['createShipments'],
        );

        // API returns empty array (no shipments created)
        $clientMock->expects(static::once())
            ->method('createShipments')
            ->willReturn([]);

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Get orders from fixture
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        $autocreate = new Dhl_Versenden_Model_Shipping_Autocreate(['logger' => $this->getLogger()]);
        $createdLabelsCount = $autocreate->autoCreate($collection);

        // Empty result from API = 0 created
        static::assertEquals(0, $createdLabelsCount);

        // Verify no shipments created when API returns empty array
        $order = $collection->getFirstItem();
        if ($order && $order->getId()) {
            $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                ->addFieldToFilter('order_id', $order->getId());
            static::assertEquals(0, $shipmentCollection->getSize(), 'No shipments should be created on empty API response');
        }
    }

    /**
     * @test
     */
    public function autoCreateConstructorRequiresLogger()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('missing or invalid argument: logger');

        // Constructor should throw when no logger provided
        new Dhl_Versenden_Model_Shipping_Autocreate([]);
    }

    /**
     * @test
     */
    public function autoCreateConstructorRequiresValidLogger()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('missing or invalid argument: logger');

        // Constructor should throw when invalid logger type provided
        new Dhl_Versenden_Model_Shipping_Autocreate(['logger' => new stdClass()]);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateMultiOrderTest
     */
    public function autoCreateWithRestClientPartialFailure()
    {
        // Test partial batch failure: 3 orders, 2 succeed, 1 fails
        // This validates $requestMap logic with multiple orders and mixed results

        // Create mock shipments for 3 orders: success, failure, success
        $shipment20 = $this->createSuccessfulShipmentMock('223344556677000', 'LABEL_DATA_0');
        $shipment22 = $this->createSuccessfulShipmentMock('223344556677002', 'LABEL_DATA_2');

        // Mock REST client - return fixed responses for 3 orders
        // Pattern: order 0 (20) succeeds, order 1 (21) fails, order 2 (22) succeeds
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['createShipments'],
        );

        $clientMock->expects(static::once())
            ->method('createShipments')
            ->willReturn([
                $shipment20,  // Order 20 succeeds
                null,         // Order 21 fails
                $shipment22,   // Order 22 succeeds
            ]);

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Get orders for autocreate (AFTER mocks are set up)
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        // Verify we have 3 orders
        static::assertEquals(3, $collection->getSize(), 'Expected 3 eligible orders in collection');

        // Execute autocreate
        $autocreate = new Dhl_Versenden_Model_Shipping_Autocreate(['logger' => $this->getLogger()]);
        $createdLabelsCount = $autocreate->autoCreate($collection);

        // Assert 2 of 3 shipments created successfully (partial batch)
        // This is the CRITICAL test: validating $requestMap handles multiple orders correctly
        static::assertEquals(
            2,
            $createdLabelsCount,
            'Should have 2 successful shipments out of 3 orders (partial failure scenario)',
        );

        // Secondary validation: Ensure we processed 3 orders
        static::assertEquals(
            3,
            $collection->getSize(),
            'Should have processed 3 orders total',
        );

        // The key validation is the count above. If $requestMap mapping was wrong,
        // we'd get array key errors or incorrect counts. The fact that we got exactly
        // 2 successes from 3 orders with our [success, null, success] mock pattern
        // proves the $requestMap logic works correctly for multiple orders.

        // Verify DB state for all 3 orders
        // Order 20 - should have shipment (success)
        $shipmentCollection20 = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 20);
        static::assertEquals(1, $shipmentCollection20->getSize(), 'Order 20 should have shipment');

        $shipment20 = $shipmentCollection20->getFirstItem();
        static::assertNotEmpty($shipment20->getShippingLabel(), 'Order 20 should have label');
        static::assertEquals('223344556677000', $shipment20->getTracksCollection()->getFirstItem()->getNumber());

        // Order 21 - should NOT have shipment (failed)
        $shipmentCollection21 = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 21);
        static::assertEquals(0, $shipmentCollection21->getSize(), 'Order 21 should NOT have shipment (API failure)');

        // Order 21 - verify error comment saved
        $order21 = Mage::getModel('sales/order')->load(21);
        $foundError = false;
        foreach ($order21->getStatusHistoryCollection() as $comment) {
            if (strpos($comment->getComment(), 'Shipment creation failed') !== false) {
                $foundError = true;
                break;
            }
        }
        static::assertTrue($foundError, 'Error comment should be saved for failed order 21');

        // Order 22 - should have shipment (success)
        $shipmentCollection22 = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', 22);
        static::assertEquals(1, $shipmentCollection22->getSize(), 'Order 22 should have shipment');

        $shipment22 = $shipmentCollection22->getFirstItem();
        static::assertNotEmpty($shipment22->getShippingLabel(), 'Order 22 should have label');
        static::assertEquals('223344556677002', $shipment22->getTracksCollection()->getFirstItem()->getNumber());
    }

    /**
     * Verify that autocreate builder includes services in shipment_service data
     * when the checkout toggle is OFF but the shipment default is ON.
     *
     * The builder must use isSelected() (shipment default) rather than
     * isEnabled() (checkout toggle) to determine the service flag value.
     *
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     * @loadFixture Model_ConfigTest_AutocreateDecoupled
     */
    public function autocreateBuilderIncludesServicesRegardlessOfCheckoutToggle()
    {
        // Order 10 is DE→DE domestic from the AutoCreateTest fixture
        $order = Mage::getModel('sales/order')->load(10);
        $shipment = $order->prepareShipment();
        $shipment->register();

        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $shipperConfig = Mage::getModel('dhl_versenden/config_shipper');
        $serviceConfig = Mage::getModel('dhl_versenden/config_service');

        $builder = new Dhl_Versenden_Model_Shipping_Autocreate_Builder(
            $order,
            $shipmentConfig,
            $shipperConfig,
            $serviceConfig,
        );

        $request = $builder->createShipmentRequest($shipment);
        $services = $request->getData('services');

        // NoNeighbourDelivery: checkout toggle OFF, shipment default ON
        static::assertNotEmpty(
            $services['shipment_service'][\Dhl\Versenden\ParcelDe\Service\NoNeighbourDelivery::CODE],
            'NoNeighbourDelivery should be active in autocreate when shipment default is ON',
        );

        // GoGreen: customer-facing only, no autocreate default (matching M2 behavior)
        static::assertArrayNotHasKey(
            \Dhl\Versenden\ParcelDe\Service\GoGreenPlus::CODE,
            $services['shipment_service'],
            'GoGreen must not be in autocreate — it is customer-facing only',
        );
    }
}
