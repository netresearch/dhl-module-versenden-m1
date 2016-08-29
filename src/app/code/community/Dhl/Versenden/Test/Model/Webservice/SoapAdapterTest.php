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
use \Dhl\Versenden\Webservice\RequestData;
use \Dhl\Versenden\Webservice\ResponseData;
use \Dhl\Versenden\Webservice\Adapter\Soap as SoapAdapter;
use \Dhl\Versenden\Webservice\Parser\Soap as SoapParser;
/**
 * Dhl_Versenden_Test_Model_Webservice_AdapterTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_SoapAdapterTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param string $serializedResponse
     */
    public function getVersion($serializedResponse)
    {
        $response = unserialize($serializedResponse);

        $major = '2';
        $minor = '1';

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->setMethods(array('getVersion'))
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient
            ->expects($this->once())
            ->method('getVersion')
            ->willReturn($response);

        $adapter = new SoapAdapter($soapClient);
        $requestData = new RequestData\Version($major, $minor, null);
        $parser = new SoapParser\Version();

        /** @var ResponseData\Version $response */
        $response = $adapter->getVersion($requestData, $parser);
        $this->assertInstanceOf(ResponseData\Version::class, $response);
        $this->assertStringStartsWith($major, $response->getVersion());
    }

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param string $serializedResponse
     * @param string $serializedRequestData
     */
    public function createShipmentOrder($serializedResponse, $serializedRequestData)
    {
        $requestData = unserialize($serializedRequestData);
        $response = unserialize($serializedResponse);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->setMethods(array('createShipmentOrder'))
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient
            ->expects($this->once())
            ->method('createShipmentOrder')
            ->willReturn($response);

        $adapter = new SoapAdapter($soapClient);
        $parser = new SoapParser\CreateShipmentOrder();

        $response = $adapter->createShipmentOrder($requestData, $parser);
        $this->assertInstanceOf(ResponseData\CreateShipment::class, $response);
        $this->assertNotNull($response->getShipmentNumber(0));
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\Adapter\NotImplementedException
     */
    public function deleteShipmentOrder()
    {
        $major = '2';
        $minor = '1';
        $requestData = new RequestData\Version($major, $minor, null);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = new SoapParser\Version();

        $adapter = new SoapAdapter($soapClient);
        $adapter->deleteShipmentOrder($requestData, $parser);
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\Adapter\NotImplementedException
     */
    public function getLabel()
    {
        $major = '2';
        $minor = '1';
        $requestData = new RequestData\Version($major, $minor, null);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = new SoapParser\Version();

        $adapter = new SoapAdapter($soapClient);
        $adapter->getLabel($requestData, $parser);
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\Adapter\NotImplementedException
     */
    public function getExportDoc()
    {
        $major = '2';
        $minor = '1';
        $requestData = new RequestData\Version($major, $minor, null);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = new SoapParser\Version();

        $adapter = new SoapAdapter($soapClient);
        $adapter->getExportDoc($requestData, $parser);
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\Adapter\NotImplementedException
     */
    public function doManifest()
    {
        $major = '2';
        $minor = '1';
        $requestData = new RequestData\Version($major, $minor, null);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = new SoapParser\Version();

        $adapter = new SoapAdapter($soapClient);
        $adapter->doManifest($requestData, $parser);
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\Adapter\NotImplementedException
     */
    public function getManifest()
    {
        $major = '2';
        $minor = '1';
        $requestData = new RequestData\Version($major, $minor, null);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = new SoapParser\Version();

        $adapter = new SoapAdapter($soapClient);
        $adapter->getManifest($requestData, $parser);
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\Adapter\NotImplementedException
     */
    public function updateShipmentOrder()
    {
        $major = '2';
        $minor = '1';
        $requestData = new RequestData\Version($major, $minor, null);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = new SoapParser\Version();

        $adapter = new SoapAdapter($soapClient);
        $adapter->updateShipmentOrder($requestData, $parser);
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\Adapter\NotImplementedException
     */
    public function validateShipment()
    {
        $major = '2';
        $minor = '1';
        $requestData = new RequestData\Version($major, $minor, null);

        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = new SoapParser\Version();

        $adapter = new SoapAdapter($soapClient);
        $adapter->validateShipment($requestData, $parser);
    }

    /**
     * @test
     */
    public function parsePackstation()
    {
        $zip = '111';
        $city = 'Foo';
        $packstationNumber = '123';
        $postNumber = '123456';
        $packStation = new RequestData\ShipmentOrder\Receiver\Packstation(
            $zip,
            $city,
            $packstationNumber,
            $postNumber
        );

        $postalFacility = SoapAdapter\PostalFacilityType::prepare($packStation);
        $this->assertInstanceOf(\Dhl\Bcs\Api\PackStationType::class, $postalFacility);
        $this->assertEquals($zip, $postalFacility->getZip());
        $this->assertEquals($city, $postalFacility->getCity());
        $this->assertEquals($packstationNumber, $postalFacility->getPackstationNumber());
        $this->assertEquals($postNumber, $postalFacility->getPostNumber());
    }

    /**
     * @test
     */
    public function parsePostfiliale()
    {
        $zip = '111';
        $city = 'Foo';
        $postfilialNumber = '123';
        $postNumber = '123456';
        $postfiliale = new RequestData\ShipmentOrder\Receiver\Postfiliale(
            $zip,
            $city,
            $postfilialNumber,
            $postNumber
        );

        $postalFacility = SoapAdapter\PostalFacilityType::prepare($postfiliale);
        $this->assertInstanceOf(\Dhl\Bcs\Api\PostfilialeType::class, $postalFacility);
        $this->assertEquals($zip, $postalFacility->getZip());
        $this->assertEquals($city, $postalFacility->getCity());
        $this->assertEquals($postfilialNumber, $postalFacility->getPostfilialNumber());
        $this->assertEquals($postNumber, $postalFacility->getPostNumber());
    }

    /**
     * @test
     */
    public function parseParcelShop()
    {
        $zip = '111';
        $city = 'Foo';
        $parcelShopNumber = '123';
        $streetName   = 'Foo Street';
        $streetNumber = '909';
        $parcelShop = new RequestData\ShipmentOrder\Receiver\ParcelShop(
            $zip,
            $city,
            $parcelShopNumber,
            $streetName,
            $streetNumber
        );

        $postalFacility = SoapAdapter\PostalFacilityType::prepare($parcelShop);
        $this->assertInstanceOf(\Dhl\Bcs\Api\ParcelShopType::class, $postalFacility);
        $this->assertEquals($zip, $postalFacility->getZip());
        $this->assertEquals($city, $postalFacility->getCity());
        $this->assertEquals($parcelShopNumber, $postalFacility->getParcelShopNumber());
        $this->assertEquals($streetName, $postalFacility->getStreetName());
        $this->assertEquals($streetNumber, $postalFacility->getStreetNumber());
    }

    /**
     * @test
     */
    public function parseUnknownFacilityType()
    {
        $facility = new RequestData\Version('2', '1');
        $postalFacility = SoapAdapter\PostalFacilityType::prepare($facility);
        $this->assertNull($postalFacility);
    }

    /**
     * @test
     */
    public function parseServices()
    {
        $serviceSelection = array(
            \Dhl\Versenden\Shipment\Service\DayOfDelivery::CODE => '2016-12-24',
            \Dhl\Versenden\Shipment\Service\DeliveryTimeFrame::CODE => '19002100',
            \Dhl\Versenden\Shipment\Service\VisualCheckOfAge::CODE => 'A21',
            \Dhl\Versenden\Shipment\Service\PreferredLocation::CODE => 'Chimney',
            \Dhl\Versenden\Shipment\Service\PreferredNeighbour::CODE => 'Santa Berger',
            \Dhl\Versenden\Shipment\Service\GoGreen::CODE => true,
            \Dhl\Versenden\Shipment\Service\Cod::CODE => 40.96,
            \Dhl\Versenden\Shipment\Service\Insurance::CODE =>  34.06,
            \Dhl\Versenden\Shipment\Service\BulkyGoods::CODE => true,

        );

        $requestData = RequestData\ShipmentOrder\ServiceSelection::fromArray($serviceSelection);
        $shipmentServices = SoapAdapter\ServiceType::prepare($requestData);
        $this->assertInstanceOf(\Dhl\Bcs\Api\ShipmentService::class, $shipmentServices);

        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\DayOfDelivery::CODE],
            $shipmentServices->getDayOfDelivery()->getDetails()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\DeliveryTimeFrame::CODE],
            $shipmentServices->getDeliveryTimeframe()->getType()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\VisualCheckOfAge::CODE],
            $shipmentServices->getVisualCheckOfAge()->getType()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\PreferredLocation::CODE],
            $shipmentServices->getPreferredLocation()->getDetails()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\PreferredNeighbour::CODE],
            $shipmentServices->getPreferredNeighbour()->getDetails()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\GoGreen::CODE],
            $shipmentServices->getGoGreen()->getActive()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\Cod::CODE],
            $shipmentServices->getCashOnDelivery()->getCodAmount()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\Insurance::CODE],
            $shipmentServices->getAdditionalInsurance()->getInsuranceAmount()
        );
        $this->assertEquals(
            $serviceSelection[\Dhl\Versenden\Shipment\Service\BulkyGoods::CODE],
            $shipmentServices->getBulkyGoods()->getActive()
        );
    }
}
