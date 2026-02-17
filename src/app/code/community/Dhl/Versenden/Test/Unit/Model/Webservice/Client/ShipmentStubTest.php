<?php

/**
 * Stub-based unit test for shipment client business logic.
 *
 * This test uses the ShipmentServiceStub to test the client wrapper logic
 * without making real HTTP calls to the DHL API.
 *
 * Note: While technically a "unit test", it requires fixture for store/config setup
 * because Client internally uses OrderBuilder which needs Magento store context.
 *
 * Speed: ~0.2 seconds (slightly slower than pure unit test due to fixture)
 * Use for: Business logic validation, error handling, request building
 * Does NOT test: Real API behavior, service combinations, sandbox limitations
 *
 * @see Dhl_Versenden_Test_Integration_LiveApi_ServicesTest for real API validation
 * @loadFixture Model_ShipmentConfigTest
 */
class Dhl_Versenden_Test_Unit_Model_Webservice_Client_ShipmentStubTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Dhl_Versenden_Test_TestDouble_ShipmentServiceStub
     */
    protected $_stub;

    /**
     * @var Dhl_Versenden_Model_Webservice_Client_Shipment
     */
    protected $_client;

    /**
     * Set up test with stub injection
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create stub
        $this->_stub = new Dhl_Versenden_Test_TestDouble_ShipmentServiceStub();

        // Create client with stub via constructor DI (no reflection needed)
        $this->_client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_stub);
    }

    /**
     * Tear down - reset stub between tests
     */
    protected function tearDown(): void
    {
        $this->_stub->reset();
        parent::tearDown();
    }

    /**
     * Test: Successful shipment creation returns shipments
     *
     * @test
     */
    public function successfulShipmentCreation()
    {
        // Arrange
        $magentoRequests = [
            $this->_createMagentoRequest('Test Shipper', 'Customer Name'),
        ];
        $orderConfig = null;

        // Act
        $result = $this->_client->createShipments($magentoRequests, $orderConfig);

        // Assert
        static::assertCount(1, $result, 'Should return one shipment');
        static::assertInstanceOf(
            'Dhl\Sdk\ParcelDe\Shipping\Api\Data\ShipmentInterface',
            $result[0],
            'Should return shipment interface',
        );
        static::assertStringStartsWith('STUB', $result[0]->getShipmentNumber(), 'Should have stub tracking number');
        static::assertStringStartsWith('%PDF-1', $result[0]->getShipmentLabel(), 'Should have PDF label');
    }

    /**
     * Test: Client captures request data for debugging
     *
     * @test
     */
    public function clientCapturesRequestData()
    {
        // Arrange
        $magentoRequests = [
            $this->_createMagentoRequest('Test Shipper', 'Customer 1'),
            $this->_createMagentoRequest('Another Shipper', 'Customer 2'),
        ];

        // Act
        $this->_client->createShipments($magentoRequests);

        // Assert
        static::assertCount(2, $this->_stub->capturedRequests, 'Stub should capture all requests');
        // Note: Captured requests are SDK objects after conversion
        static::assertNotEmpty($this->_stub->capturedRequests[0], 'First request should be captured');
        static::assertNotEmpty($this->_stub->capturedRequests[1], 'Second request should be captured');
    }

    /**
     * Test: Service exception is caught and re-thrown
     *
     * @test
     */
    public function serviceExceptionHandling()
    {
        // Arrange
        $exception = new \Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException('Simulated API error');
        $this->_stub->setException($exception);

        $magentoRequests = [$this->_createMagentoRequest('Test Shipper', 'Customer')];

        // Assert exception is expected
        $this->expectException(\Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException::class);
        $this->expectExceptionMessage('Simulated API error');

        // Act (exception should be thrown)
        $this->_client->createShipments($magentoRequests);
    }

    /**
     * Test: Detailed service exception is caught and re-thrown
     *
     * @test
     */
    public function detailedServiceExceptionHandling()
    {
        // Arrange
        $exception = new \Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException('Validation failed');
        $this->_stub->setException($exception);

        $magentoRequests = [$this->_createMagentoRequest('Test Shipper', 'Customer')];

        // Assert exception is expected
        $this->expectException(\Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException::class);
        $this->expectExceptionMessage('Validation failed');

        // Act (exception should be thrown)
        $this->_client->createShipments($magentoRequests);
    }

    /**
     * Test: Successful cancellation returns shipment numbers
     *
     * @test
     */
    public function successfulShipmentCancellation()
    {
        // Arrange
        $shipmentNumbers = ['STUB0000000001', 'STUB0000000002'];

        // Act
        $result = $this->_client->cancelShipments($shipmentNumbers);

        // Assert - SDK returns string[] for successful cancellations
        static::assertCount(2, $result, 'Should return two shipment numbers');
        static::assertEquals('STUB0000000001', $result[0]);
        static::assertEquals('STUB0000000002', $result[1]);
    }

    /**
     * Test: Multiple shipments get unique tracking numbers
     *
     * @test
     */
    public function uniqueTrackingNumbers()
    {
        // Arrange
        $magentoRequests = [
            $this->_createMagentoRequest('Test1', 'Customer1'),
            $this->_createMagentoRequest('Test2', 'Customer2'),
            $this->_createMagentoRequest('Test3', 'Customer3'),
        ];

        // Act
        $result = $this->_client->createShipments($magentoRequests);

        // Assert
        $trackingNumbers = array_map(function ($shipment) {
            return $shipment->getShipmentNumber();
        }, $result);

        static::assertCount(3, array_unique($trackingNumbers), 'All tracking numbers should be unique');
        static::assertEquals('STUB0000000001', $trackingNumbers[0]);
        static::assertEquals('STUB0000000002', $trackingNumbers[1]);
        static::assertEquals('STUB0000000003', $trackingNumbers[2]);
    }

    /**
     * Helper: Create a Magento shipment request for testing
     *
     * Uses fixture data (Model_ShipmentConfigTest) instead of mocks.
     *
     * @param string $shipperName Unused (kept for backward compatibility)
     * @param string $recipientName Unused (kept for backward compatibility)
     * @return Mage_Shipping_Model_Shipment_Request
     */
    protected function _createMagentoRequest($shipperName, $recipientName)
    {
        // Load shipment from fixture (requires @loadFixture Model_ShipmentConfigTest on class)
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        // Create Magento request with fixture data
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('gk_api_product', 'V01PAK');
        $request->setPackageParams([
            '0' => [
                'params' => [
                    'weight' => 2.5,
                    'length' => 30,
                    'width' => 20,
                    'height' => 10,
                ],
            ],
        ]);

        // Add service info structure (required by ServiceBuilder)
        $request->setData('services', [
            'shipment_service' => [],
            'service_setting' => [],
        ]);

        return $request;
    }
}
