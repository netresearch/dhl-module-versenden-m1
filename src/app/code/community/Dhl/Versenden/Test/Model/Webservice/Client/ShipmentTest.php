<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException;
use Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException;
use Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService;

class Dhl_Versenden_Test_Model_Webservice_Client_ShipmentTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Dhl_Versenden_Model_Webservice_Client_Shipment
     */
    protected $_client;

    /**
     * @var ShipmentService|PHPUnit\Framework\MockObject\MockObject
     */
    protected $_serviceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock shipment service
        $this->_serviceMock = $this->getMockBuilder(ShipmentService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipperConfigTest
     */
    public function clientInitialization()
    {
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment();

        static::assertInstanceOf(
            Dhl_Versenden_Model_Webservice_Client_Shipment::class,
            $client,
        );
    }

    /**
     * Test successful shipment creation with SDK mock.
     *
     * Integration test: Uses real fixtures, real validation, real order building.
     * Only mocks the SDK service (HTTP call to DHL API).
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function createShipmentsSuccess()
    {
        // Arrange - Create request from real fixture data (like CarrierRestIntegrationTest)
        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('gk_api_product', 'V01PAK');
        $request->setData('services', ['shipment_service' => [], 'service_setting' => []]);
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

        // SDK returns ShipmentInterface objects carrying the request index;
        // the client re-keys them to the input request keys
        $expectedShipment = new \Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\Shipment(0, 'SHIP-1', '', 'bGFiZWw=', '', '', '');

        // Mock SDK to return successful response
        $this->_serviceMock
            ->expects(static::once())
            ->method('createShipments')
            ->willReturn([$expectedShipment]);

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Act - Full integration: validation → order building → SDK call
        $result = $client->createShipments([$request]);

        // Assert
        static::assertSame([0 => $expectedShipment], $result);
    }

    /**
     * Test SDK DetailedServiceException propagation.
     *
     * Integration test: Uses real fixtures, real validation, real order building.
     * Only mocks the SDK service (HTTP call to DHL API).
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function createShipmentsDetailedException()
    {
        // Arrange - Create request from real fixture data
        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('gk_api_product', 'V01PAK');
        $request->setData('services', ['shipment_service' => [], 'service_setting' => []]);
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

        $exception = new DetailedServiceException('Detailed error message');

        // Mock SDK to throw DetailedServiceException
        $this->_serviceMock
            ->expects(static::once())
            ->method('createShipments')
            ->willThrowException($exception);

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Assert exception is thrown
        $this->expectException(DetailedServiceException::class);
        $this->expectExceptionMessage('Detailed error message');

        // Act - Full integration: validation → order building → SDK call → exception
        $client->createShipments([$request]);
    }

    /**
     * Test SDK ServiceException propagation.
     *
     * Integration test: Uses real fixtures, real validation, real order building.
     * Only mocks the SDK service (HTTP call to DHL API).
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function createShipmentsServiceException()
    {
        // Arrange - Create request from real fixture data
        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('gk_api_product', 'V01PAK');
        $request->setData('services', ['shipment_service' => [], 'service_setting' => []]);
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

        $exception = new ServiceException('Generic service error');

        // Mock SDK to throw ServiceException
        $this->_serviceMock
            ->expects(static::once())
            ->method('createShipments')
            ->willThrowException($exception);

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Assert exception is thrown
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage('Generic service error');

        // Act - Full integration: validation → order building → SDK call → exception
        $client->createShipments([$request]);
    }

    /**
     * @test
     */
    public function cancelShipmentsSuccess()
    {
        // Arrange
        $shipmentNumbers = ['123456789', '987654321'];

        $expectedResponse = ['cancellation1', 'cancellation2'];

        $this->_serviceMock
            ->expects(static::once())
            ->method('cancelShipments')
            ->with($shipmentNumbers)
            ->willReturn($expectedResponse);

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Act
        $result = $client->cancelShipments($shipmentNumbers);

        // Assert
        static::assertSame($expectedResponse, $result);
    }

    /**
     * @test
     */
    public function cancelShipmentsDetailedException()
    {
        // Arrange
        $shipmentNumbers = ['123456789'];

        $exception = new DetailedServiceException('Cancellation failed with details');

        $this->_serviceMock
            ->expects(static::once())
            ->method('cancelShipments')
            ->with($shipmentNumbers)
            ->willThrowException($exception);

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Assert exception is thrown
        $this->expectException(DetailedServiceException::class);
        $this->expectExceptionMessage('Cancellation failed with details');

        // Act
        $client->cancelShipments($shipmentNumbers);
    }

    /**
     * @test
     */
    public function cancelShipmentsServiceException()
    {
        // Arrange
        $shipmentNumbers = ['123456789'];

        $exception = new ServiceException('Cancellation service error');

        $this->_serviceMock
            ->expects(static::once())
            ->method('cancelShipments')
            ->with($shipmentNumbers)
            ->willThrowException($exception);

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Assert exception is thrown
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage('Cancellation service error');

        // Act
        $client->cancelShipments($shipmentNumbers);
    }

    // =========================================================================
    // VALIDATION INTEGRATION TESTS
    // =========================================================================

    /**
     * Test that validation failure prevents SDK shipment creation and sets error on request.
     *
     * Note: This test verifies validation integration without needing complete shipper config
     * since validation fails before SDK building begins.
     *
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function createShipmentsWithValidationFailure()
    {
        // Create request that will fail validation (partial shipment + COD)
        $shipment = Mage::getModel('sales/order_shipment')->load(2); // Partial + COD
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('gk_api_product', 'V01PAK');
        $request->setData('services', ['shipment_service' => [], 'service_setting' => []]);

        // Mock the service to verify it's NOT called (validation prevents API call)
        $this->_serviceMock
            ->expects(static::never())
            ->method('createShipments');

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Act
        $result = $client->createShipments([$request]);

        // Assert
        static::assertEmpty($result, 'No shipments should be created for invalid request');
        static::assertNotEmpty(
            $request->getData('request_data_exception'),
            'Validation error should be set on request',
        );
        static::assertStringContainsString(
            'Cannot do partial shipment with COD',
            $request->getData('request_data_exception'),
        );
    }

    /**
     * Note: Valid request test omitted due to fixture conflicts.
     * Valid request flow is already tested in CarrierRestIntegrationTest.
     * These validation integration tests focus on failure scenarios and empty guard logic.
     */

    /**
     * Test empty collection guard when all requests fail validation.
     *
     * Note: Uses ValidatorTest fixtures which have partial shipments + COD for testing
     * validation logic without needing complete shipper config.
     *
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function createShipmentsAllInvalidEmptyGuard()
    {
        // Create multiple invalid requests that will both fail validation
        $shipment1 = Mage::getModel('sales/order_shipment')->load(2); // Partial + COD
        $request1 = new Mage_Shipping_Model_Shipment_Request();
        $request1->setOrderShipment($shipment1);
        $request1->setData('gk_api_product', 'V01PAK');
        $request1->setData('services', ['shipment_service' => [], 'service_setting' => []]);

        $shipment2 = Mage::getModel('sales/order_shipment')->load(3); // Partial + Insurance
        $request2 = new Mage_Shipping_Model_Shipment_Request();
        $request2->setOrderShipment($shipment2);
        $request2->setData('gk_api_product', 'V01PAK');
        $request2->setData('services', [
            'shipment_service' => ['additionalInsurance' => '1'],
            'service_setting' => [],
        ]);

        // Mock service should NEVER be called (empty collection guard)
        $this->_serviceMock
            ->expects(static::never())
            ->method('createShipments');

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Act
        $result = $client->createShipments([$request1, $request2]);

        // Assert
        static::assertEmpty($result, 'No shipments created when all invalid');
        static::assertNotEmpty($request1->getData('request_data_exception'));
        static::assertNotEmpty($request2->getData('request_data_exception'));
    }

    /**
     * A builder InvalidArgumentException (HTSUS, pDDP guard) must degrade to a
     * per-item request_data_exception instead of aborting the whole batch.
     *
     * No fixtures: the order builder is swapped for a mock via the client stub,
     * and the validator singleton is replaced with a no-op mock so that plain,
     * fixture-less shipment requests reach the builder stage.
     *
     * @test
     */
    public function convertStoresBuilderExceptionPerItem()
    {
        $this->mockValidatorAsNoOp();

        $failingMessage = 'PDDP is not allowed for USA/Puerto Rico shipments with value > 800.00 USD.';

        $orderBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $orderBuilder->expects(static::exactly(2))
            ->method('build')
            ->willReturnOnConsecutiveCalls(
                static::throwException(new \InvalidArgumentException($failingMessage)),
                // Healthy path: the client's own new ShipmentOrderRequestBuilder() is not
                // injectable, so create() runs for real afterwards. populateSdkBuilder()
                // fills the minimum fields it requires so create() succeeds instead of
                // throwing RequestValidatorException, letting us assert the batch really
                // continues past the failing item.
                static::returnCallback([$this, 'populateSdkBuilder']),
            );

        $failingRequest = $this->createShipmentRequest();
        $healthyRequest = $this->createShipmentRequest();

        $client = $this->createClientStub($orderBuilder);
        $sdkShipments = $client->convertToSdkShipments([1 => $failingRequest, 2 => $healthyRequest]);

        static::assertSame($failingMessage, $failingRequest->getData('request_data_exception'));
        static::assertNull($healthyRequest->getData('request_data_exception'));
        static::assertArrayHasKey(2, $sdkShipments);
        static::assertArrayNotHasKey(1, $sdkShipments);
    }

    /**
     * A middle-item drop must not corrupt the API payload (gap keys serialize
     * as JSON object) nor the response-to-request mapping.
     *
     * @test
     */
    public function createShipmentsNormalizesPayloadAndKeepsRequestKeys()
    {
        $this->mockValidatorAsNoOp();

        $failingMessage = 'HS code must be between 6 and 11 digits (item 1)';

        // order builder: first request fails, second and third succeed
        $orderBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $orderBuilder->expects(static::exactly(3))
            ->method('build')
            ->willReturnOnConsecutiveCalls(
                static::throwException(new \InvalidArgumentException($failingMessage)),
                static::returnCallback([$this, 'populateSdkBuilder']),
                static::returnCallback([$this, 'populateSdkBuilder']),
            );

        // SDK service: assert the payload arrives as a LIST (sequential keys),
        // answer with Shipment objects carrying the payload positions 0 and 1
        $sdkService = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentServiceInterface::class)
            ->getMock();
        $sdkService->expects(static::once())
            ->method('createShipments')
            ->with(static::callback(function (array $payload) {
                return array_keys($payload) === range(0, count($payload) - 1);
            }))
            ->willReturnCallback(function (array $payload) {
                return [
                    new \Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\Shipment(0, 'SHIP-A', '', 'bGFiZWw=', '', '', ''),
                    new \Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\Shipment(1, 'SHIP-B', '', 'bGFiZWw=', '', '', ''),
                ];
            });

        $requests = [
            7 => $this->createShipmentRequest(),
            8 => $this->createShipmentRequest(),
            9 => $this->createShipmentRequest(),
        ];

        $client = $this->createClientStub($orderBuilder, $sdkService);
        $shipments = $client->createShipments($requests);

        // request 7 dropped locally; 8 and 9 map back to their ORIGINAL keys
        static::assertArrayNotHasKey(7, $shipments);
        static::assertSame('SHIP-A', $shipments[8]->getShipmentNumber());
        static::assertSame('SHIP-B', $shipments[9]->getShipmentNumber());
        static::assertSame($failingMessage, $requests[7]->getData('request_data_exception'));
    }

    /**
     * When DHL rejects an item, the SDK's create response mapper skips it and
     * only returns Shipment objects for the accepted request indices. The
     * surviving items must come back under their ORIGINAL request keys with
     * the rejected item's key absent - the DHL-side half of the contract.
     *
     * @test
     */
    public function createShipmentsOmitsDhlRejectedItemsUnderOriginalKeys()
    {
        $this->mockValidatorAsNoOp();

        // order builder: all three requests convert successfully
        $orderBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $orderBuilder->expects(static::exactly(3))
            ->method('build')
            ->willReturnCallback([$this, 'populateSdkBuilder']);

        // SDK service: DHL rejects the middle item of the 3-item payload, so
        // the mapper only returns Shipments for request indices 0 and 2
        $sdkService = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentServiceInterface::class)
            ->getMock();
        $sdkService->expects(static::once())
            ->method('createShipments')
            ->willReturnCallback(function (array $payload) {
                return [
                    new \Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\Shipment(0, 'SHIP-A', '', 'bGFiZWw=', '', '', ''),
                    new \Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\Shipment(2, 'SHIP-C', '', 'bGFiZWw=', '', '', ''),
                ];
            });

        $requests = [
            7 => $this->createShipmentRequest(),
            8 => $this->createShipmentRequest(),
            9 => $this->createShipmentRequest(),
        ];

        $client = $this->createClientStub($orderBuilder, $sdkService);
        $shipments = $client->createShipments($requests);

        // middle item failed at DHL; first and third keep their ORIGINAL keys
        static::assertSame('SHIP-A', $shipments[7]->getShipmentNumber());
        static::assertArrayNotHasKey(8, $shipments);
        static::assertSame('SHIP-C', $shipments[9]->getShipmentNumber());
    }

    /**
     * Minimal Magento shipment request that reaches the builder stage.
     *
     * The order shipment only needs to satisfy Order::build()'s type hint - the
     * mocked order builder never touches its data - so an unloaded model instance
     * is enough and no sales fixtures are required.
     *
     * @return Mage_Shipping_Model_Shipment_Request
     */
    private function createShipmentRequest()
    {
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment(Mage::getModel('sales/order_shipment'));
        $request->setData('gk_api_product', 'V01PAK');
        $request->setData('services', ['shipment_service' => [], 'service_setting' => []]);

        return $request;
    }

    /**
     * Populate a minimal-but-valid SDK builder so create() succeeds.
     *
     * The client's own new ShipmentOrderRequestBuilder() is not injectable, so
     * builder mocks that want the "healthy path" to reach a real create() call
     * need the minimum fields filled in (shipper reference/account, recipient
     * address, shipment details, package weight) or create() throws
     * RequestValidatorException.
     *
     * Public so it can be referenced as [$this, 'populateSdkBuilder'] from
     * PHPUnit's willReturnCallback(), which invokes it outside class scope.
     *
     * @param \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder $sdkBuilder
     */
    public function populateSdkBuilder($sdkBuilder)
    {
        $sdkBuilder->setShipperReference('shipper-ref')
            ->setShipperAccount('123456789')
            ->setRecipientAddress('Recipient', 'DE', '12345', 'Berlin', 'Teststreet')
            ->setShipmentDetails('V01PAK', new \DateTime())
            ->setPackageDetails(1.0);
    }

    /**
     * Replace the validator singleton with a no-op mock.
     *
     * The requests built by createShipmentRequest() carry no real order/payment data,
     * so the real validator (which inspects order totals and payment method) would
     * fatal rather than validate. This test is about the builder catch/continue
     * behavior, not validation, so validation is short-circuited.
     */
    private function mockValidatorAsNoOp()
    {
        $validatorMock = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Validator::class)
            ->getMock();
        $this->replaceByMock('singleton', 'dhl_versenden/webservice_builder_validator', $validatorMock);
    }

    /**
     * @param Dhl_Versenden_Model_Webservice_Builder_Order $orderBuilder
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentServiceInterface|null $service Defaults to the class-level service mock
     * @return Dhl_Versenden_Test_Model_Webservice_Client_ShipmentStub
     */
    private function createClientStub($orderBuilder, $service = null)
    {
        $client = new Dhl_Versenden_Test_Model_Webservice_Client_ShipmentStub($service ?? $this->_serviceMock);
        $client->orderBuilder = $orderBuilder;

        return $client;
    }
}

/**
 * Stub exposing _convertToSdkShipments with an injectable order builder
 * (no reflection in tests — override the protected members instead).
 */
class Dhl_Versenden_Test_Model_Webservice_Client_ShipmentStub extends Dhl_Versenden_Model_Webservice_Client_Shipment
{
    /** @var Dhl_Versenden_Model_Webservice_Builder_Order */
    public $orderBuilder;

    public function convertToSdkShipments(array $magentoRequests)
    {
        return $this->_convertToSdkShipments($magentoRequests);
    }

    protected function _createOrderBuilder()
    {
        return $this->orderBuilder;
    }
}
