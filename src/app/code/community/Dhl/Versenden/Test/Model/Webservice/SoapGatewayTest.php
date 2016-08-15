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
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Webservice;
/**
 * Dhl_Versenden_Test_Model_Webservice_SoapGatewayTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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

        $productCode    = Dhl_Versenden_Model_Shipping_Carrier_Versenden::PRODUCT_CODE_PAKET_NATIONAL;
        $packageWeight  = 1.2;

        $receiver         = $shipmentOrder->getReceiver();
        $serviceSelection = $shipmentOrder->getServiceSelection();

        $helperMock = $this->getHelperMock(
            'dhl_versenden/webservice',
            array('utcToCet', 'serviceSelectionToServiceSettings', 'shippingAddressToReceiver')
        );
        $helperMock
            ->expects($this->once())
            ->method('utcToCet')
            ->willReturn($shipmentDate);
        $helperMock
            ->expects($this->once())
            ->method('serviceSelectionToServiceSettings')
            ->willReturn($serviceSelection);
        $helperMock
            ->expects($this->once())
            ->method('shippingAddressToReceiver')
            ->willReturn($receiver);
        $this->replaceByMock('helper', 'dhl_versenden/webservice', $helperMock);

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setData('dhl_versenden_info', '');
        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId($incrementId);
        $order->setShippingAddress($shippingAddress);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);
        $shipment->setStoreId(2);


        $packageInfo = array(
            array(
                'params' => array(
                    'container' => $productCode,
                    'weight'    => $packageWeight,
                ),
                'items' => array()
            )
        );
        $serviceInfo = array(
            'shipment_service' => array(),
            'service_setting' => array(),
        );

        $shipmentOrder = Mage::getModel('dhl_versenden/webservice_gateway_soap')
            ->shipmentToShipmentOrder($sequenceNumber, $shipment, $packageInfo, $serviceInfo);

        $this->assertEquals($shipmentOrder->getReceiver()->getName1(), $expectation->getReceiverName1());
        $this->assertEquals(
            $shipmentOrder->getServiceSelection()->isBulkyGoods(),
            $expectation->isServiceSettingsBulkyGoods()
        );
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipmentConfigTest
     * @loadFixture Model_ShipperConfigTest
     *
     * @param string $jsonInfo
     */
    public function shipmentToShipmentOrderWithJson($jsonInfo)
    {
        $sequenceNumber = '303';
        $incrementId    = '808';
        $shipmentDate   = '2016-12-24';

        $productCode    = Dhl_Versenden_Model_Shipping_Carrier_Versenden::PRODUCT_CODE_PAKET_NATIONAL;
        $packageWeight  = 1.2;

        $helperMock = $this->getHelperMock(
            'dhl_versenden/webservice',
            array('utcToCet')
        );
        $helperMock
            ->expects($this->once())
            ->method('utcToCet')
            ->willReturn($shipmentDate);
        $this->replaceByMock('helper', 'dhl_versenden/webservice', $helperMock);

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setData('dhl_versenden_info', $jsonInfo);
        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId($incrementId);
        $order->setShippingAddress($shippingAddress);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);
        $shipment->setStoreId(2);


        $packageInfo = array(
            array(
                'params' => array(
                    'container' => $productCode,
                    'weight'    => $packageWeight,
                ),
                'items' => array()
            )
        );
        $serviceInfo = array(
            'shipment_service' => array(),
            'service_setting' => array(),
        );

        $shipmentOrder = Mage::getModel('dhl_versenden/webservice_gateway_soap')
            ->shipmentToShipmentOrder($sequenceNumber, $shipment, $packageInfo, $serviceInfo);

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
            new Webservice\ResponseData\Status(0, 'ok', 'ok'),
            new Webservice\ResponseData\LabelCollection(),
            array($sequenceNumber => $shipmentNumber)
        );

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment(new Mage_Sales_Model_Order_Shipment());
        $request->setData('packages', array());
        $request->setData('services', array());
        $shipmentRequests = array($sequenceNumber => $request);


        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($wsResponse);

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder', 'getAdapter', 'logRequest', 'logResponse'))
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
            ->expects($this->never())
            ->method('logRequest');
        $gateway
            ->expects($this->never())
            ->method('logResponse');


        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $result = $gateway->createShipmentOrder($shipmentRequests);
        $this->assertEquals($wsResponse, $result);

        $this->assertEventDispatched('dhl_versenden_create_shipment_order_before');
        $this->assertEventDispatched('dhl_versenden_create_shipment_order_after');
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     * @expectedException Webservice\RequestData\ValidationException
     *
     * @param Webservice\RequestData\ShipmentOrder $shipmentOrder
     * @param \Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function createShipmentOrderRequestDataException($shipmentOrder, $expectation)
    {
        $customerReference = '303';
        $validationError   = 'too erroneous!';
        $frontendMessage   = "The shipment request(s) had errors. #$customerReference: $validationError";

        $shipmentOrder = new Mage_Sales_Model_Order();
        $shipmentOrder->setIncrementId($customerReference);
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($shipmentOrder);

        $sequenceNumber = 'foo';

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('packages', array());
        $request->setData('services', array());
        $shipmentRequests = array($sequenceNumber => $request);

        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder'))
            ->getMock();
        $gateway
            ->expects($this->once())
            ->method('shipmentToShipmentOrder')
            ->willThrowException(new Webservice\RequestData\ValidationException($validationError));


        $this->setExpectedException(
            Webservice\RequestData\ValidationException::class,
            $frontendMessage
        );
        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $gateway->createShipmentOrder($shipmentRequests);
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
        $shipmentRequests = array($sequenceNumber => $request);


        $adapter = $this->getMockBuilder(Webservice\Adapter\Soap::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createShipmentOrder'))
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willThrowException(new SoapFault('soap:Server', 'my bad :('));


        $gateway = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Gateway_Soap::class)
            ->setMethods(array('shipmentToShipmentOrder', 'getAdapter', 'logRequest', 'logResponse'))
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
            ->method('logRequest');
        $gateway
            ->expects($this->once())
            ->method('logResponse');


        /** @var Dhl_Versenden_Model_Webservice_Gateway_Soap $gateway */
        $gateway->createShipmentOrder($shipmentRequests);
    }

    /**
     * @test
     */
    public function log()
    {
        $lastRequest = 'last request';
        $lastResponse = 'last response';
        $lastResponseHeaders = 'last response headers';

        $clientMock = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__getLastRequest', '__getLastResponse', '__getLastResponseHeaders'))
            ->getMock();
        $clientMock
            ->expects($this->once())
            ->method('__getLastRequest')
            ->willReturn($lastRequest);
        $clientMock
            ->expects($this->once())
            ->method('__getLastResponse')
            ->willReturn($lastResponse);
        $clientMock
            ->expects($this->once())
            ->method('__getLastResponseHeaders')
            ->willReturn($lastResponseHeaders);

        $loggerMock = $this->getModelMock('core/logger', array('log'));
        $loggerMock
            ->expects($this->exactly(2))
            ->method('log')
            ->withConsecutive(
                $this->equalTo($lastRequest),
                $this->equalTo($lastResponse . "\n\n" . $lastResponseHeaders)
            );
        $this->replaceByMock('singleton', 'core/logger', $loggerMock);

        $adapter = new Webservice\Adapter\Soap($clientMock);

        $gateway = new Dhl_Versenden_Model_Webservice_Gateway_Soap();
        $gateway->logRequest($adapter);
        $gateway->logResponse($adapter);
    }
}
