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
 * Dhl_Versenden_Test_Model_Shipping_Carrier_VersendenTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
    public function getContainerTypes()
    {
        $paketNational = Dhl_Versenden_Model_Shipping_Carrier_Versenden::PRODUCT_CODE_PAKET_NATIONAL;
        $paketInternational = Dhl_Versenden_Model_Shipping_Carrier_Versenden::PRODUCT_CODE_WELTPAKET;

        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();

        $shipperCountry = 'DE';
        $receiverCountry = 'DE';
        $params = new Varien_Object(array(
            'method' => 'dhlversenden_foo',
            'country_shipper' => $shipperCountry,
            'country_recipient' => $receiverCountry,
        ));
        $containerTypes = $carrier->getContainerTypes($params);
        $this->assertInternalType('array', $containerTypes);
        $this->assertArrayHasKey($paketNational, $containerTypes);
        $this->assertArrayNotHasKey($paketInternational, $containerTypes);


        $shipperCountry = 'AT';
        $receiverCountry = 'DE';
        $params = new Varien_Object(array(
            'method' => 'dhlversenden_foo',
            'country_shipper' => $shipperCountry,
            'country_recipient' => $receiverCountry,
        ));
        $containerTypes = $carrier->getContainerTypes($params);
        $this->assertInternalType('array', $containerTypes);
        $this->assertArrayHasKey($paketInternational, $containerTypes);
        $this->assertArrayNotHasKey($paketNational, $containerTypes);

        $shipperCountry = 'AT';
        $receiverCountry = 'CH';
        $params = new Varien_Object(array(
            'method' => 'dhlversenden_foo',
            'country_shipper' => $shipperCountry,
            'country_recipient' => $receiverCountry,
        ));
        $containerTypes = $carrier->getContainerTypes($params);
        $this->assertInternalType('array', $containerTypes);
        $this->assertArrayHasKey($paketInternational, $containerTypes);
        $this->assertArrayNotHasKey($paketNational, $containerTypes);

    }

    /**
     * @test
     */
    public function requestToShipmentOk()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $request = new Mage_Shipping_Model_Shipment_Request();

        $trackingNumber = 'foo';
        $labelContent = 'bar';

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
                'status' => new Dhl\Versenden\Webservice\ResponseData\Status(0, 'ok', 'ok'),
                'label'  => $labelContent,
            )));

        $result = new Varien_Object(array(
            'shipment_number' => $trackingNumber,
            'labels' => $labels
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
     * @expectedException \Dhl\Versenden\Webservice\ResponseData\StatusException
     */
    public function requestToShipmentStatusException()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();
        $request = new Mage_Shipping_Model_Shipment_Request();

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
                'status' => new Dhl\Versenden\Webservice\ResponseData\Status(1010, 'nok', 'nok'),
            )));

        $result = new Varien_Object(array(
            'labels' => $labels
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
     * @expectedException \Exception
     */
    public function requestToShipmentException()
    {
        $carrier = new Dhl_Versenden_Model_Shipping_Carrier_Versenden();

        $request = new Mage_Shipping_Model_Shipment_Request();

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
        $this->assertArrayHasKey('G', $units);
        $this->assertArrayHasKey('KG', $units);

        $this->assertFalse($carrier->getCode('unit_of_measure', 'LBS'));
        $this->assertStringStartsWith('Gram', $carrier->getCode('unit_of_measure', 'G'));
    }
}
