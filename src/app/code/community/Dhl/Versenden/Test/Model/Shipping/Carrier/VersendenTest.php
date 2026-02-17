<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Shipping_Carrier_VersendenTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function collectRates()
    {
        $rateRequest = new Mage_Shipping_Model_Rate_Request();
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        static::assertNull($carrier->collectRates($rateRequest));
    }

    /**
     * @test
     */
    public function getAllowedMethods()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $methods = $carrier->getAllowedMethods();
        static::assertIsArray($methods);
        static::assertEmpty($methods);
    }

    /**
     * @test
     */
    public function isShippingLabelsAvailable()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        static::assertTrue($carrier->isShippingLabelsAvailable());
    }

    /**
     * @test
     */
    public function getProductsGermanShipper()
    {
        $paketNational = \Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL;
        $paketMerchandise = \Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET;
        $paketInternational = \Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET;

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $shipperCountry = 'DE';

        $receiverCountry = 'DE';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        static::assertIsArray($products);
        static::assertArrayHasKey($paketNational, $products);
        static::assertArrayHasKey($paketMerchandise, $products);
        static::assertArrayNotHasKey($paketInternational, $products);
        static::assertNotEmpty(\Dhl\Versenden\ParcelDe\Product::getProcedure($paketNational));

        // eu receiver
        $receiverCountry = 'AT';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        static::assertIsArray($products);
        static::assertArrayNotHasKey($paketNational, $products);
        static::assertArrayNotHasKey($paketMerchandise, $products);
        static::assertArrayHasKey($paketInternational, $products);

        // row receiver
        $receiverCountry = 'NZ';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        static::assertIsArray($products);
        static::assertArrayNotHasKey($paketNational, $products);
        static::assertArrayNotHasKey($paketMerchandise, $products);
        static::assertArrayHasKey($paketInternational, $products);
        static::assertNotEmpty(\Dhl\Versenden\ParcelDe\Product::getProcedure($paketInternational));
    }

    /**
     * @test
     */
    public function getProductsInvalidShipper()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();

        // no shipper or receiver info given
        $products = $carrier->getProducts(null, null);
        static::assertIsArray($products);
        static::assertNotEmpty($products);

        // international shipper, national receiver
        $shipperCountry = 'CZ';
        $receiverCountry = 'DE';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        static::assertIsArray($products);
        static::assertCount(0, $products);

        // international shipper, international receiver
        $shipperCountry = 'CZ';
        $receiverCountry = 'CH';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        static::assertIsArray($products);
        static::assertCount(0, $products);
        static::assertEmpty(\Dhl\Versenden\ParcelDe\Product::getProcedure('V77FOO'));
    }

    /**
     * @test
     */
    public function getContentTypes()
    {
        $params = new Varien_Object();
        $contentTypes = [
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_COMMERCIAL_GOODS,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_COMMERCIAL_SAMPLE,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_DOCUMENT,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_PRESENT,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_RETURN_OF_GOODS,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_OTHER,
        ];

        $helperMock = $this->getHelperMock('dhl_versenden/data', ['isCollectCustomsData']);
        $helperMock
            ->expects(static::exactly(2))
            ->method('isCollectCustomsData')
            ->willReturnOnConsecutiveCalls(false, true);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        static::assertEmpty($carrier->getContentTypes($params));
        static::assertEquals($contentTypes, array_keys($carrier->getContentTypes($params)));
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentOk()
    {
        // Load shipment from fixture (EcomDev_PHPUnit populates test DB)
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
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

        $trackingNumber = 'foo';

        // Create valid base64-encoded PDF (Zend adapter needs real PDF data)
        $pdf = new Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $labelBase64 = base64_encode($pdf->render());

        // Mock REST SDK shipment response
        $mockShipment = $this->getMockBuilder('stdClass')
            ->addMethods(['getShipmentNumber', 'getLabels'])
            ->getMock();
        $mockShipment
            ->expects(static::any())
            ->method('getShipmentNumber')
            ->willReturn($trackingNumber);
        $mockShipment
            ->expects(static::any())
            ->method('getLabels')
            ->willReturn([$labelBase64]);

        // Mock REST client (replaces SOAP gateway)
        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', [
            'createShipments',
        ]);
        $clientMock
            ->expects(static::once())
            ->method('createShipments')
            ->willReturn([$mockShipment]);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        $response = $carrier->requestToShipment($request);
        $info = $response->getData('info');

        static::assertIsArray($info);
        static::assertCount(1, $info);
        static::assertEquals($trackingNumber, $info[0]['tracking_number']);
        // Label content is raw binary PDF (Zend adapter merges and returns binary)
        static::assertStringStartsWith('%PDF-', $info[0]['label_content']);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentStatusException()
    {
        $this->expectException(\Mage_Core_Exception::class);

        // Load shipment from fixture with complete entity data
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
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

        // Mock REST client to throw DetailedServiceException (converts to Mage_Core_Exception)
        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', ['createShipments']);
        $clientMock
            ->expects(static::once())
            ->method('createShipments')
            ->willThrowException(new \Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException('Status error'));
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        $carrier->requestToShipment($request);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentException()
    {
        $this->expectException(\Exception::class);

        // Load shipment from fixture with complete entity data
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
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

        // Mock REST client to throw generic Exception
        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', ['createShipments']);
        $clientMock
            ->expects(static::once())
            ->method('createShipments')
            ->willThrowException(new \Exception('Test exception'));
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        $carrier->requestToShipment($request);
    }

    /**
     * @test
     */
    public function getCode()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        static::assertFalse($carrier->getCode('foo'));

        $units = $carrier->getCode('unit_of_measure');
        static::assertIsArray($units);
        static::assertNotEmpty($units);
        static::assertCount(2, $units);
        static::assertArrayHasKey('G', $units);
        static::assertArrayHasKey('KG', $units);

        static::assertFalse($carrier->getCode('unit_of_measure', 'LBS'));
        static::assertStringStartsWith('Gram', $carrier->getCode('unit_of_measure', 'G'));

        // Test terms_of_trade code type
        $terms = $carrier->getCode('terms_of_trade');
        static::assertIsArray($terms);
        static::assertCount(4, $terms);
        static::assertEquals('DDP', $carrier->getCode('terms_of_trade', 'DDP'));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getTrackingInfo()
    {
        $trackingCode = 'trackfoo';

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $trackingStatus = $carrier->getTrackingInfo($trackingCode);

        static::assertInstanceOf(Mage_Shipping_Model_Tracking_Result_Status::class, $trackingStatus);
        static::assertEquals('foo', $trackingStatus->getData('carrier_title'));
        static::assertEquals(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE, $trackingStatus->getData('carrier'));
        static::assertEquals($trackingCode, $trackingStatus->getData('tracking'));
        static::assertStringEndsWith($trackingCode, $trackingStatus->getData('url'));
    }

    /**
     * Test product auto-selection when no product is provided
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentAutoSelectsProduct()
    {
        // Load domestic shipment (DE->DE)
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        // DON'T set gk_api_product - let carrier auto-select
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

        $trackingNumber = '00340434161094015902';

        // Create valid base64-encoded PDF (Zend adapter needs real PDF data)
        $pdf = new Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $labelBase64 = base64_encode($pdf->render());

        // Mock REST SDK shipment response
        $mockShipment = $this->getMockBuilder('stdClass')
            ->addMethods(['getShipmentNumber', 'getLabels'])
            ->getMock();
        $mockShipment
            ->expects(static::any())
            ->method('getShipmentNumber')
            ->willReturn($trackingNumber);
        $mockShipment
            ->expects(static::any())
            ->method('getLabels')
            ->willReturn([$labelBase64]);

        // Mock REST client
        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', ['createShipments']);
        $clientMock
            ->expects(static::once())
            ->method('createShipments')
            ->with(
                static::callback(function ($requests) {
                    // Verify product was auto-selected
                    return isset($requests[0]) && $requests[0]->getData('gk_api_product') === 'V01PAK';
                }),
                static::anything(),
            )
            ->willReturn([$mockShipment]);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        $response = $carrier->requestToShipment($request);

        static::assertInstanceOf('Varien_Object', $response);
        static::assertTrue($response->hasData('info'));
    }

    /**
     * Test that requestToShipment() throws exception when no product is available
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentThrowsExceptionWhenNoProductAvailable()
    {
        $this->expectException(\Mage_Core_Exception::class);
        $this->expectExceptionMessage('No DHL product available for this shipment.');

        // Clear any POST data from previous tests (LiveApi tests may pollute this)
        $httpRequest = Mage::app()->getFrontController()->getRequest();
        $httpRequest->setPost('shipping_product', null);
        $httpRequest->setPost('shipment_service', []);
        $httpRequest->setPost('service_setting', []);
        $httpRequest->setPost('customs', []);

        // Load shipment
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        // Create carrier mock that returns empty products array
        $carrierMock = $this->getMockBuilder(Dhl_Versenden_Model_Shipping_Carrier_Versenden::class)
            ->setMethods(['getProducts'])
            ->getMock();
        $carrierMock
            ->expects(static::once())
            ->method('getProducts')
            ->willReturn([]); // No products available

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        // DON'T set gk_api_product - let carrier try auto-select
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

        $carrierMock->requestToShipment($request);
    }

    /**
     * Test collectRates returns null (carrier does not calculate rates)
     *
     * @test
     */
    public function collectRatesReturnsNull()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $request = new Mage_Shipping_Model_Rate_Request();

        $result = $carrier->collectRates($request);

        static::assertNull($result);
    }

    /**
     * Test getContentTypes() with registered shipment (covers postcode extraction)
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function getContentTypesWithRegisteredShipment()
    {
        // Load shipment and register it
        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        Mage::register('current_shipment', $shipment);

        $params = new Varien_Object([
            'country_shipper' => 'DE',
            'country_recipient' => 'US',
        ]);

        // Mock helper to return true (international shipment)
        $helperMock = $this->getHelperMock('dhl_versenden/data', ['isCollectCustomsData']);
        $helperMock
            ->expects(static::once())
            ->method('isCollectCustomsData')
            ->willReturn(true);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $contentTypes = $carrier->getContentTypes($params);

        static::assertNotEmpty($contentTypes);
        static::assertCount(6, $contentTypes);

        // Clean up registry
        Mage::unregister('current_shipment');
    }

    /**
     * Test requestToShipment() throws exception when client returns empty shipments array
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentThrowsExceptionWhenNoShipmentsReturned()
    {
        $this->expectException(\Mage_Core_Exception::class);
        $this->expectExceptionMessage('The shipment request had errors.');

        // Load shipment
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
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

        // Mock REST client to return empty array
        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', ['createShipments']);
        $clientMock
            ->expects(static::once())
            ->method('createShipments')
            ->willReturn([]);  // Empty shipments array triggers exception
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        $carrier->requestToShipment($request);
    }

    /**
     * Test requestToShipment() handles InvalidArgumentException from builders
     *
     * This covers the exception handling for validation errors thrown by builders
     * (e.g., CustomsBuilder when international shipment data is incomplete).
     *
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentHandlesInvalidArgumentException()
    {
        $this->expectException(\Mage_Core_Exception::class);
        $this->expectExceptionMessage('Content type required for international shipments');

        // Load shipment
        $shipment = Mage::getModel('sales/order_shipment')->load(1);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
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

        // Mock REST client to throw InvalidArgumentException (validation error from builder)
        $clientMock = $this->getModelMock('dhl_versenden/webservice_client_shipment', ['createShipments']);
        $clientMock
            ->expects(static::once())
            ->method('createShipments')
            ->willThrowException(new \InvalidArgumentException('Content type required for international shipments'));
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        $carrier->requestToShipment($request);
    }
}
