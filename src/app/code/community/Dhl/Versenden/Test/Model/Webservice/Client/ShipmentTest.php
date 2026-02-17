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

        $expectedResponse = ['shipment1', 'shipment2'];

        // Mock SDK to return successful response
        $this->_serviceMock
            ->expects(static::once())
            ->method('createShipments')
            ->willReturn($expectedResponse);

        // Create client with mocked service via constructor DI
        $client = new Dhl_Versenden_Model_Webservice_Client_Shipment($this->_serviceMock);

        // Act - Full integration: validation → order building → SDK call
        $result = $client->createShipments([$request]);

        // Assert
        static::assertSame($expectedResponse, $result);
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
}
