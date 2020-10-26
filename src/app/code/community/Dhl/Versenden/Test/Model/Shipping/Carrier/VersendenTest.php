<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

class Dhl_Versenden_Test_Model_Shipping_Carrier_VersendenTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function collectRates()
    {
        $rateRequest = new Mage_Shipping_Model_Rate_Request();
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $this->assertNull($carrier->collectRates($rateRequest));
    }

    /**
     * @test
     */
    public function getAllowedMethods()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $methods = $carrier->getAllowedMethods();
        $this->assertInternalType('array', $methods);
        $this->assertEmpty($methods);
    }

    /**
     * @test
     */
    public function isShippingLabelsAvailable()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $this->assertTrue($carrier->isShippingLabelsAvailable());
    }

    /**
     * @test
     */
    public function getProductsGermanShipper()
    {
        $paketNational = \Dhl\Versenden\Bcs\Api\Product::CODE_PAKET_NATIONAL;
        $paketMerchandise = \Dhl\Versenden\Bcs\Api\Product::CODE_WARENPOST_NATIONAL;
        $paketInternational = \Dhl\Versenden\Bcs\Api\Product::CODE_WELTPAKET;

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $shipperCountry = 'DE';

        $receiverCountry = 'DE';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        $this->assertInternalType('array', $products);
        $this->assertArrayHasKey($paketNational, $products);
        $this->assertArrayHasKey($paketMerchandise, $products);
        $this->assertArrayNotHasKey($paketInternational, $products);
        $this->assertNotEmpty(\Dhl\Versenden\Bcs\Api\Product::getProcedure($paketNational));

        // eu receiver
        $receiverCountry = 'AT';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        $this->assertInternalType('array', $products);
        $this->assertArrayNotHasKey($paketNational, $products);
        $this->assertArrayNotHasKey($paketMerchandise, $products);
        $this->assertArrayHasKey($paketInternational, $products);

        // row receiver
        $receiverCountry = 'NZ';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        $this->assertInternalType('array', $products);
        $this->assertArrayNotHasKey($paketNational, $products);
        $this->assertArrayNotHasKey($paketMerchandise, $products);
        $this->assertArrayHasKey($paketInternational, $products);
        $this->assertNotEmpty(\Dhl\Versenden\Bcs\Api\Product::getProcedure($paketInternational));
    }

    /**
     * @test
     */
    public function getProductsInvalidShipper()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();

        // no shipper or receiver info given
        $products = $carrier->getProducts(null, null);
        $this->assertInternalType('array', $products);
        $this->assertNotEmpty($products);

        // international shipper, national receiver
        $shipperCountry = 'CZ';
        $receiverCountry = 'DE';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        $this->assertInternalType('array', $products);
        $this->assertCount(0, $products);

        // international shipper, international receiver
        $shipperCountry = 'CZ';
        $receiverCountry = 'CH';
        $products = $carrier->getProducts($shipperCountry, $receiverCountry);
        $this->assertInternalType('array', $products);
        $this->assertCount(0, $products);
        $this->assertEmpty(\Dhl\Versenden\Bcs\Api\Product::getProcedure('V77FOO'));
    }

    /**
     * @test
     */
    public function getContentTypes()
    {
        $params = new Varien_Object();
        $contentTypes = array(
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_COMMERCIAL_SAMPLE,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_DOCUMENT,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_OTHER,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_PRESENT,
            Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_RETURN_OF_GOODS,
        );

        $helperMock = $this->getHelperMock('dhl_versenden/data', array('isCollectCustomsData'));
        $helperMock
            ->expects($this->exactly(2))
            ->method('isCollectCustomsData')
            ->willReturnOnConsecutiveCalls(false, true);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $this->assertEmpty($carrier->getContentTypes($params));
        $this->assertEquals($contentTypes, array_keys($carrier->getContentTypes($params)));
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function requestToShipmentOk()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $request = new Mage_Shipping_Model_Shipment_Request();

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setCountryId('DE');
        $order = new Mage_Sales_Model_Order();
        $order->setShippingAddress($shippingAddress);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setStoreId(1);
        $shipment->setOrder($order);
        $request->setOrderShipment($shipment);

        $trackingNumber = 'foo';
        $labelContent = 'bar';

        $label = $this->getMockBuilder(Varien_Object::class)
            ->setMethods(array('__call', 'getAllLabels'))
            ->getMock();
        $label
            ->expects($this->any())
            ->method('__call')
            ->with(
                $this->equalTo('getStatus'),
                $this->anything()
            )
            ->willReturn(new ResponseData\Status\Response(0, 'ok', array('ok')));
        $label
            ->expects($this->any())
            ->method('getAllLabels')
            ->willReturn($labelContent);

        $labels = $this->getMockBuilder(Varien_Object::class)
            ->setMethods(array('__call'))
            ->getMock();
        $labels
            ->expects($this->any())
            ->method('__call')
            ->with(
                $this->equalTo('getItem'),
                $this->anything()
            )
            ->willReturn($label);

        $result = new Varien_Object(array(
            'shipment_number' => $trackingNumber,
            'created_items' => $labels
        ));

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array(
            'createShipmentOrder'
        ));
        $gatewayMock
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($result);
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $response = $carrier->requestToShipment($request);
        $info = $response->getData('info');

        $this->assertInternalType('array', $info);
        $this->assertCount(1, $info);
        $this->assertEquals($trackingNumber, $info[0]['tracking_number']);
        $this->assertEquals($labelContent, $info[0]['label_content']);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     * @expectedException \Mage_Core_Exception
     */
    public function requestToShipmentStatusException()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $request = new Mage_Shipping_Model_Shipment_Request();

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setCountryId('DE');
        $order = new Mage_Sales_Model_Order();
        $order->setShippingAddress($shippingAddress);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setStoreId(1);
        $shipment->setOrder($order);
        $request->setOrderShipment($shipment);

        $labels = $this->getMockBuilder(Varien_Object::class)
            ->setMethods(array('__call'))
            ->getMock();
        $labels
            ->expects($this->any())
            ->method('__call')
            ->with(
                $this->equalTo('getItem'),
                $this->anything()
            )
            ->willReturn(new Varien_Object(array(
                'status' => new ResponseData\Status\Response(1010, 'nok', array('nok')),
            )));

        $result = new Varien_Object(array(
            'created_items' => $labels
        ));


        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array(
            'createShipmentOrder'
        ));
        $gatewayMock
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($result);
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $carrier->requestToShipment($request);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     * @expectedException \Exception
     */
    public function requestToShipmentException()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $request = new Mage_Shipping_Model_Shipment_Request();

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setCountryId('DE');
        $order = new Mage_Sales_Model_Order();
        $order->setShippingAddress($shippingAddress);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setStoreId(1);
        $shipment->setOrder($order);
        $request->setOrderShipment($shipment);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array(
            'createShipmentOrder'
        ));
        $gatewayMock
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willThrowException(new \Exception('e'));
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $carrier->requestToShipment($request);
    }

    /**
     * @test
     */
    public function getCode()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $this->assertFalse($carrier->getCode('foo'));

        $units = $carrier->getCode('unit_of_measure');
        $this->assertInternalType('array', $units);
        $this->assertNotEmpty($units);
        $this->assertCount(2, $units);
        $this->assertArrayHasKey('G', $units);
        $this->assertArrayHasKey('KG', $units);

        $this->assertFalse($carrier->getCode('unit_of_measure', 'LBS'));
        $this->assertStringStartsWith('Gram', $carrier->getCode('unit_of_measure', 'G'));
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

        $this->assertInstanceOf(Mage_Shipping_Model_Tracking_Result_Status::class, $trackingStatus);
        $this->assertEquals('foo', $trackingStatus->getData('carrier_title'));
        $this->assertEquals(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE, $trackingStatus->getData('carrier'));
        $this->assertEquals($trackingCode, $trackingStatus->getData('tracking'));
        $this->assertStringEndsWith($trackingCode, $trackingStatus->getData('url'));
    }
}
