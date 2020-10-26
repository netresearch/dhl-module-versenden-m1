<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice;

class Dhl_Versenden_Test_Model_Webservice_SoapGatewayTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @fixture Model_SoapGatewayTest
     */
    public function getAdapter()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $gateway = new Dhl_Versenden_Model_Webservice_Gateway_Soap();
        $soapAdapter = $gateway->getAdapter($config);

        $this->assertInstanceOf(Webservice\Adapter\Soap::class, $soapAdapter);

        $soapClient = $soapAdapter->getClient();
        $this->assertInstanceOf(SoapClient::class, $soapClient);
    }

    /**
     * @test
     */
    public function getParser()
    {
        $gateway = new Dhl_Versenden_Model_Webservice_Gateway_Soap();
        $parser = $gateway->getParser(Dhl_Versenden_Model_Webservice_Gateway_Soap::OPERATION_GET_VERSION);
        $this->assertInstanceOf(Webservice\Parser::class, $parser);

        $parser = $gateway->getParser(Dhl_Versenden_Model_Webservice_Gateway_Soap::OPERATION_CREATE_SHIPMENT_ORDER);
        $this->assertInstanceOf(Webservice\Parser::class, $parser);

        $parser = $gateway->getParser('operation accomplished');
        $this->assertNull($parser);
    }

    /**
     * @test
     */
    public function getLogger()
    {
        $config = new Dhl_Versenden_Model_Config();
        $writer = new Dhl_Versenden_Model_Logger_Writer();
        $gateway = new Dhl_Versenden_Model_Webservice_Gateway_Soap();
        $soapLogger = $gateway->getLogger($config, $writer);
        $this->assertInstanceOf(Dhl_Versenden_Model_Webservice_Logger_Soap::class, $soapLogger);
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipmentConfigTest
     * @loadFixture Model_ShipperConfigTest
     *
     * @param Webservice\RequestData\ShipmentOrder $shipmentOrder
     * @param \Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function shipmentToShipmentOrder($shipmentOrder, $expectation)
    {
        $sequenceNumber = '303';
        $incrementId    = '808';
        $shipmentDate   = '2016-12-24';

        $productCode    = \Dhl\Versenden\Bcs\Api\Product::CODE_PAKET_NATIONAL;
        $packageWeight  = 1.2;

        $receiver         = $shipmentOrder->getReceiver();
        $serviceSelection = $shipmentOrder->getServiceSelection();

        $helperMock = $this->getHelperMock(
            'dhl_versenden/data',
            array('utcToCet', 'serviceSelectionToServiceSettings', 'shippingAddressToReceiver')
        );
        $helperMock
            ->expects($this->exactly(2))
            ->method('utcToCet')
            ->willReturn($shipmentDate);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);

        $receiverBuilderMock = $this->getModelMock(
            'dhl_versenden/webservice_builder_receiver',
            array('getReceiver'),
            false,
            array(),
            '',
            false
        );
        $receiverBuilderMock
            ->expects($this->once())
            ->method('getReceiver')
            ->willReturn($receiver);
        $this->replaceByMock('model', 'dhl_versenden/webservice_builder_receiver', $receiverBuilderMock);

        $serviceBuilderMock = $this->getModelMock(
            'dhl_versenden/webservice_builder_service',
            array('getServiceSelection'),
            false,
            array(),
            '',
            false
        );
        $serviceBuilderMock
            ->expects($this->exactly(2))
            ->method('getServiceSelection')
            ->willReturn($serviceSelection);
        $this->replaceByMock('model', 'dhl_versenden/webservice_builder_service', $serviceBuilderMock);

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setData('dhl_versenden_info', '');
        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId($incrementId);
        $order->setShippingAddress($shippingAddress);
        $order->setPayment($payment);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);
        $shipment->setStoreId(2);


        $packageInfo = array(
            array(
                'params' => array('weight' => $packageWeight),
                'items' => array()
            )
        );
        $serviceInfo = array(
            'shipment_service' => array(),
            'service_setting' => array(),
        );
        $customsInfo = array();

        $shipmentOrder = Mage::getModel('dhl_versenden/webservice_gateway_soap')->shipmentToShipmentOrder(
            $sequenceNumber,
            $shipment,
            $packageInfo,
            $serviceInfo,
            $customsInfo,
            $productCode
        );

        $this->assertEquals($shipmentOrder->getReceiver()->getName1(), $expectation->getReceiverName1());
        $this->assertEquals(
            $shipmentOrder->getServiceSelection()->isBulkyGoods(),
            $expectation->isServiceSettingsBulkyGoods()
        );

        // Check correct Response if using a non existing product
        $productCode = 'Foo';
        $shipmentOrder = Mage::getModel('dhl_versenden/webservice_gateway_soap')->shipmentToShipmentOrder(
            $sequenceNumber,
            $shipment,
            $packageInfo,
            $serviceInfo,
            $customsInfo,
            $productCode
        );

        $this->assertEmpty($shipmentOrder->getReturnShipmentAccountNumber());
    }

    /**
     * @dataProvider dataProvider
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipmentConfigTest
     * @loadFixture Model_ShipperConfigTest
     *
     * @param string $jsonInfo
     */
    public function shipmentToShipmentOrderWithJson($jsonInfo)
    {
        $info = \Dhl\Versenden\Bcs\Api\Info\Serializer::unserialize($jsonInfo);

        $sequenceNumber = '303';
        $incrementId    = '808';
        $shipmentDate   = '2016-12-24';

        $productCode    = \Dhl\Versenden\Bcs\Api\Product::CODE_PAKET_NATIONAL;
        $packageWeight  = 1200;

        $helperMock = $this->getHelperMock(
            'dhl_versenden/data',
            array('utcToCet')
        );
        $helperMock
            ->expects($this->once())
            ->method('utcToCet')
            ->willReturn($shipmentDate);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setData('dhl_versenden_info', $info);
        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId($incrementId);
        $order->setShippingAddress($shippingAddress);
        $order->setPayment($payment);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);
        $shipment->setStoreId(2);


        $packageInfo = array(
            array(
                'params' => array('weight' => $packageWeight),
                'items' => array()
            )
        );
        $serviceInfo = array(
            'shipment_service' => array(),
            'service_setting' => array(),
        );
        $customsInfo = array();

        $shipmentOrder = Mage::getModel('dhl_versenden/webservice_gateway_soap')->shipmentToShipmentOrder(
            $sequenceNumber,
            $shipment,
            $packageInfo,
            $serviceInfo,
            $customsInfo,
            $productCode
        );

        $this->assertEquals($sequenceNumber, $shipmentOrder->getSequenceNumber());
        $this->assertEquals($incrementId, $shipmentOrder->getReference());
        $this->assertEquals($productCode, $shipmentOrder->getProductCode());
        $this->assertEquals($shipmentDate, $shipmentOrder->getShipmentDate());

        $packages = $shipmentOrder->getPackages();
        $this->assertInstanceOf(Webservice\RequestData\ShipmentOrder\PackageCollection::class, $packages);
        $this->assertCount(1, $packages);
        /** @var Webservice\RequestData\ShipmentOrder\Package $package */
        foreach ($packages as $package) {
            $this->assertEquals($packageWeight, $package->getWeightInKG());
        }
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     *
     * @param Webservice\RequestData\ShipmentOrder $shipmentOrder
     * @param \Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function createShipmentOrderOk($shipmentOrder, $expectation)
    {
        $sequenceNumber = 'foo';
        $shipmentNumber = 'bar';

        $wsResponse = new Webservice\ResponseData\CreateShipment(
            new Webservice\ResponseData\Status\Response(0, 'ok', array('ok')),
            new Webservice\ResponseData\CreateShipment\LabelCollection(),
            array($sequenceNumber => $shipmentNumber)
        );

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment(new Mage_Sales_Model_Order_Shipment());
        $request->setData('packages', array());
        $request->setData('services', array());
        $request->setData('customs', array());
        $shipmentRequests = array($sequenceNumber => $request);


        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($wsResponse);

        $logger = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Logger_Soap::class)
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $logger
            ->expects($this->once())
            ->method('debug');
        $logger
            ->expects($this->never())
            ->method('warning');
        $logger
            ->expects($this->never())
            ->method('error');

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder', 'getAdapter', 'getLogger'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('shipmentToShipmentOrder')
            ->willReturn($shipmentOrder);
        $gateway
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);
        $gateway
            ->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);


        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $result = $gateway->createShipmentOrder($shipmentRequests);
        $this->assertEquals($wsResponse, $result);

        $this->assertEventDispatched('dhl_versenden_create_shipment_order_before');
        $this->assertEventDispatched('dhl_versenden_create_shipment_order_after');
    }


    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     *
     * @param Webservice\RequestData\ShipmentOrder $shipmentOrder
     * @param \Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function createShipmentOrderErrorStatus($shipmentOrder, $expectation)
    {
        $sequenceNumber = 'foo';
        $shipmentNumber = 'bar';

        $wsResponse = new Webservice\ResponseData\CreateShipment(
            new Webservice\ResponseData\Status\Response(23, 'ok', array('ok')),
            new Webservice\ResponseData\CreateShipment\LabelCollection(),
            array($sequenceNumber => $shipmentNumber)
        );

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment(new Mage_Sales_Model_Order_Shipment());
        $request->setData('packages', array());
        $request->setData('services', array());
        $request->setData('customs', array());
        $shipmentRequests = array($sequenceNumber => $request);


        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($wsResponse);

        $logger = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Logger_Soap::class)
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $logger
            ->expects($this->never())
            ->method('debug');
        $logger
            ->expects($this->once())
            ->method('warning');
        $logger
            ->expects($this->never())
            ->method('error');

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder', 'getAdapter', 'getLogger'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('shipmentToShipmentOrder')
            ->willReturn($shipmentOrder);
        $gateway
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);
        $gateway
            ->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);


        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $result = $gateway->createShipmentOrder($shipmentRequests);
        $this->assertEquals($wsResponse, $result);

        $this->assertEventDispatched('dhl_versenden_create_shipment_order_before');
        $this->assertEventDispatched('dhl_versenden_create_shipment_order_after');
    }

    /**
     * @test
     */
    public function createShipmentOrderRequestDataException()
    {
        $customerReference = '303';
        $validationError   = 'too erroneous!';
        $frontendMessage   = "The shipment request(s) had errors. #$customerReference: $validationError";

        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId($customerReference);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);

        $sequenceNumber = 'foo';

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('packages', array());
        $request->setData('services', array());
        $request->setData('customs', array());
        $shipmentRequests = array($sequenceNumber => $request);

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('shipmentToShipmentOrder')
            ->willThrowException(new Webservice\RequestData\ValidationException($validationError));

        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $result = $gateway->createShipmentOrder($shipmentRequests);
        $this->assertNull($result);
        $this->assertTrue($request->hasData('request_data_exception'));
        $this->assertSame($validationError, $request->getData('request_data_exception'));
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     *
     * @param Webservice\RequestData\ShipmentOrder $shipmentOrder
     * @param \Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function createShipmentOrderPartialShipmentException($shipmentOrder, $expectation)
    {
        $customerReference = '303';
        $validationError   = 'Cannot do partial shipment with COD or Additional Insurance.';

        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId($customerReference);
        $order->setTotalQtyOrdered(77);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setTotalQty(22);
        $shipment->setOrder($order);

        $sequenceNumber = 'foo';
        $serviceInfo = array(
            'shipment_service' => array(\Dhl\Versenden\Bcs\Api\Shipment\Service\Insurance::CODE),
        );

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('packages', array());
        $request->setData('services', $serviceInfo);
        $request->setData('customs', array());
        $shipmentRequests = array($sequenceNumber => $request);

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('shipmentToShipmentOrder')
            ->willReturn($shipmentOrder);

        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $result = $gateway->createShipmentOrder($shipmentRequests);
        $this->assertNull($result);
        $this->assertTrue($request->hasData('request_data_exception'));
        $this->assertSame($validationError, $request->getData('request_data_exception'));
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     * @expectedException SoapFault
     *
     * @param Webservice\RequestData\ShipmentOrder $shipmentOrder
     * @param \Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function createShipmentOrderLogFault($shipmentOrder, $expectation)
    {
        $sequenceNumber = 'foo';

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment(new Mage_Sales_Model_Order_Shipment());
        $request->setData('packages', array());
        $request->setData('services', array());
        $request->setData('customs', array());
        $shipmentRequests = array($sequenceNumber => $request);


        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willThrowException(new SoapFault('soap:Server', 'my bad :('));

        $logger = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Logger_Soap::class)
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $logger
            ->expects($this->never())
            ->method('debug');
        $logger
            ->expects($this->never())
            ->method('warning');
        $logger
            ->expects($this->once())
            ->method('error');

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder', 'getAdapter', 'getLogger'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('shipmentToShipmentOrder')
            ->willReturn($shipmentOrder);
        $gateway
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);
        $gateway
            ->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);

        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $gateway->createShipmentOrder($shipmentRequests);
    }

    /**
     * @test
     */
    public function deleteShipmentOrderOk()
    {
        $shipmentNumbers = array('123', '456');

        $wsResponse = new Webservice\ResponseData\DeleteShipment(
            new Webservice\ResponseData\Status\Response(0, 'ok', array('')),
            new Webservice\ResponseData\DeleteShipment\StatusCollection()
        );

        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('deleteShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('deleteShipmentOrder')
            ->willReturn($wsResponse);

        $logger = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Logger_Soap::class)
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $logger
            ->expects($this->once())
            ->method('debug');
        $logger
            ->expects($this->never())
            ->method('warning');
        $logger
            ->expects($this->never())
            ->method('error');

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('getAdapter', 'getLogger'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);
        $gateway
            ->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);

        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $result = $gateway->deleteShipmentOrder($shipmentNumbers);
        $this->assertEquals($wsResponse, $result);

        $this->assertEventDispatched('dhl_versenden_delete_shipment_order_before');
        $this->assertEventDispatched('dhl_versenden_delete_shipment_order_after');
    }

    /**
     * @test
     */
    public function deleteShipmentOrderErrorStatus()
    {
        $shipmentNumbers = array('123', '456');

        $wsResponse = new Webservice\ResponseData\DeleteShipment(
            new Webservice\ResponseData\Status\Response(12, 'ok', array('')),
            new Webservice\ResponseData\DeleteShipment\StatusCollection()
        );

        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('deleteShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('deleteShipmentOrder')
            ->willReturn($wsResponse);

        $logger = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Logger_Soap::class)
            ->setMethods(array('debug', 'error', 'warning'))
            ->disableOriginalConstructor()
            ->getMock();
        $logger
            ->expects($this->never())
            ->method('debug');
        $logger
            ->expects($this->once())
            ->method('warning');
        $logger
            ->expects($this->never())
            ->method('error');

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('getAdapter', 'getLogger'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);
        $gateway
            ->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);

        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $result = $gateway->deleteShipmentOrder($shipmentNumbers);
        $this->assertEquals($wsResponse, $result);

        $this->assertEventDispatched('dhl_versenden_delete_shipment_order_before');
        $this->assertEventDispatched('dhl_versenden_delete_shipment_order_after');
    }

    /**
     * @test
     * @expectedException SoapFault
     */
    public function deleteShipmentOrderLogFault()
    {
        $shipmentNumbers = array('123', '456');

        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('deleteShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('deleteShipmentOrder')
            ->willThrowException(new SoapFault('soap:Server', 'my bad :('));

        $logger = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Logger_Soap::class)
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $logger
            ->expects($this->never())
            ->method('debug');
        $logger
            ->expects($this->never())
            ->method('warning');
        $logger
            ->expects($this->once())
            ->method('error');

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('getAdapter', 'getLogger'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);
        $gateway
            ->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);

        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $gateway->deleteShipmentOrder($shipmentNumbers);
    }
}
