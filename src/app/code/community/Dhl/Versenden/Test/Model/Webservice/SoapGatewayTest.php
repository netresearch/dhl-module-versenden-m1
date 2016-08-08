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
     * @fixture SoapGateway
     */
    public function getAdapter()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $gateway = new Dhl_Versenden_Model_Webservice_Gateway_Soap();
        $soapAdapter = $gateway->getAdapter($config);

        $this->assertInstanceOf(\Dhl\Versenden\Webservice\Adapter\Soap::class, $soapAdapter);

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
        $this->assertInstanceOf(\Dhl\Versenden\Webservice\Parser::class, $parser);

        $parser = $gateway->getParser(Dhl_Versenden_Model_Webservice_Gateway_Soap::OPERATION_CREATE_SHIPMENT_ORDER);
        $this->assertInstanceOf(\Dhl\Versenden\Webservice\Parser::class, $parser);

        $parser = $gateway->getParser('operation accomplished');
        $this->assertNull($parser);
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     *
     * @param $shipmentOrder
     * @param $expectation
     */
    public function createShipmentOrderOk($shipmentOrder, $expectation)
    {
        $sequenceNumber = 'foo';
        $shipmentNumber = 'bar';

        $wsResponse = new \Dhl\Versenden\Webservice\ResponseData\CreateShipment(
            new \Dhl\Versenden\Webservice\ResponseData\Status(0, 'ok', 'ok'),
            new \Dhl\Versenden\Webservice\ResponseData\LabelCollection(),
            array($sequenceNumber => $shipmentNumber)
        );

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment(new Mage_Sales_Model_Order_Shipment());
        $shipmentRequests = array($sequenceNumber => $request);


        $adapter = $this->getMockBuilder(\Dhl\Versenden\Webservice\Adapter\Soap::class)
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


        $result = $gateway->createShipmentOrder($shipmentRequests);
        $this->assertEquals($wsResponse, $result);

        $this->assertEventDispatched('dhl_versenden_create_shipment_order_before');
        $this->assertEventDispatched('dhl_versenden_create_shipment_order_after');
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider
     * @expectedException SoapFault
     *
     * @param $shipmentOrder
     * @param $expectation
     */
    public function createShipmentOrderLogFault($shipmentOrder, $expectation)
    {
        $sequenceNumber = 'foo';

        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment(new Mage_Sales_Model_Order_Shipment());
        $shipmentRequests = array($sequenceNumber => $request);


        $adapter = $this->getMockBuilder(\Dhl\Versenden\Webservice\Adapter\Soap::class)
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

        $adapter = new \Dhl\Versenden\Webservice\Adapter\Soap($clientMock);

        $gateway = new Dhl_Versenden_Model_Webservice_Gateway_Soap();
        $gateway->logRequest($adapter);
        $gateway->logResponse($adapter);
    }
}
