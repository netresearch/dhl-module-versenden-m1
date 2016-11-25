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
use \Dhl\Versenden\Bcs\Api\Shipment\Service;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

/**
 * Dhl_Versenden_Test_Model_Shipping_InfoTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Shipping_InfoTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param string $jsonInfo
     */
    public function loadDhlVersendenInfo($jsonInfo)
    {
        $shippingInfo = \Dhl\Versenden\Bcs\Api\Info\Serializer::unserialize($jsonInfo);
        $this->assertInstanceOf(
            \Dhl\Versenden\Bcs\Api\Info::class,
            $shippingInfo
        );
        $this->assertInstanceOf(
            \Dhl\Versenden\Bcs\Api\Info\Services::class,
            $shippingInfo->getServices()
        );
        $this->assertInstanceOf(
            \Dhl\Versenden\Bcs\Api\Info\Receiver::class,
            $shippingInfo->getReceiver()
        );
    }

    /**
     * Assert that after unserialization all information is still the same as
     * before serialization.
     *
     * @test
     */
    public function shiftDhlVersendenInfo()
    {
        // prepare service settings
        $preferredLocation = 'Foo';

        $streetName        = 'Street';
        $streetNumber      = '303';
        $countryCode       = 'DE';

        $packstationNumber = '404';
        $postfilialNumber  = '505';
        $parcelShopNumber  = '606';
        $postNumber        = '707';


        // create and serialize shipping info
        $shippingInfo = new \Dhl\Versenden\Bcs\Api\Info();
        $shippingInfo->getServices()->preferredLocation = $preferredLocation;

        $shippingInfo->getReceiver()->streetName = $streetName;
        $shippingInfo->getReceiver()->streetNumber = $streetNumber;
        $shippingInfo->getReceiver()->countryISOCode = $countryCode;

        $shippingInfo->getReceiver()->getPackstation()->packstationNumber = $packstationNumber;
        $shippingInfo->getReceiver()->getPackstation()->postNumber = $postNumber;
        $shippingInfo->getReceiver()->getPackstation()->countryISOCode = $countryCode;

        $shippingInfo->getReceiver()->getPostfiliale()->postfilialNumber = $postfilialNumber;
        $shippingInfo->getReceiver()->getPostfiliale()->postNumber = $postNumber;
        $shippingInfo->getReceiver()->getPostfiliale()->countryISOCode = $countryCode;

        $shippingInfo->getReceiver()->getParcelShop()->parcelShopNumber = $parcelShopNumber;
        $shippingInfo->getReceiver()->getParcelShop()->streetName = $streetName;
        $shippingInfo->getReceiver()->getParcelShop()->streetNumber = $streetNumber;
        $shippingInfo->getReceiver()->getParcelShop()->countryISOCode = $countryCode;

        $json = \Dhl\Versenden\Bcs\Api\Info\Serializer::serialize($shippingInfo);


        // create shipping info from serialized string
        $unserialized = \Dhl\Versenden\Bcs\Api\Info\Serializer::unserialize($json);

        $this->assertEquals($preferredLocation, $unserialized->getServices()->preferredLocation);
        $this->assertNull($unserialized->getServices()->preferredNeighbour);

        $this->assertEquals($streetNumber, $unserialized->getReceiver()->streetNumber);
        $this->assertEquals($streetName, $unserialized->getReceiver()->streetName);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->countryISOCode);

        $this->assertEquals($packstationNumber, $unserialized->getReceiver()->getPackstation()->packstationNumber);
        $this->assertEquals($postNumber, $unserialized->getReceiver()->getPackstation()->postNumber);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->getPackstation()->countryISOCode);

        $this->assertEquals($postfilialNumber, $unserialized->getReceiver()->getPostfiliale()->postfilialNumber);
        $this->assertEquals($postNumber, $unserialized->getReceiver()->getPostfiliale()->postNumber);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->getPostfiliale()->countryISOCode);

        $this->assertEquals($parcelShopNumber, $unserialized->getReceiver()->getParcelShop()->parcelShopNumber);
        $this->assertEquals($streetName, $unserialized->getReceiver()->getParcelShop()->streetName);
        $this->assertEquals($streetNumber, $unserialized->getReceiver()->getParcelShop()->streetNumber);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->getParcelShop()->countryISOCode);
    }

    /**
     * @test
     */
    public function updateVersendenInfo()
    {
        // set service #1
        $preferredLocation = 'Foo';
        $versendenInfo = new \Dhl\Versenden\Bcs\Api\Info();
        $versendenInfo->getServices()->preferredLocation = $preferredLocation;

        // check if service is included
        $services = $versendenInfo->getServices()->toArray();
        $this->assertArrayHasKey('preferred_location', $services);
        $this->assertEquals($preferredLocation, $services['preferred_location']);

        // add service #2
        $preferredNeighbour = 'Bar';
        $services['preferred_neighbour'] = $preferredNeighbour;
        $versendenInfo->getServices()->fromArray($services);

        // check if services are included
        $this->assertEquals($preferredNeighbour, $versendenInfo->getServices()->preferredNeighbour);
        $this->assertEquals($preferredLocation, $versendenInfo->getServices()->preferredLocation);

        // add packstation
        $stationId = '808';
        $streetNumber = '303';
        $postalFacility = array(
            'packstation_number' => $stationId,
            'post_number' => '12345678'
        );
        $receiver = array(
            'packstation' => $postalFacility,
        );
        $versendenInfo->getReceiver()->fromArray($receiver);

        // check if packstation is included
        $this->assertSame($stationId, $versendenInfo->getReceiver()->getPackstation()->packstationNumber);

        // add address data
        $receiver = $versendenInfo->getReceiver()->toArray();
        $receiver['street'] = 'Place de la Foo';
        $receiver['street_number'] = $streetNumber;
        $versendenInfo->getReceiver()->fromArray($receiver);

        $this->assertSame($stationId, $versendenInfo->getReceiver()->getPackstation()->packstationNumber);
        $this->assertSame($streetNumber, $versendenInfo->getReceiver()->streetNumber);
    }

    /**
     * @test
     */
    public function wrongSchemaVersion()
    {
        $versendenInfo = new \Dhl\Versenden\Bcs\Api\Info();
        $schemaVersion = 'Foo';
        $preferredLocation = 'Bar';

        $versendenInfo->schemaVersion = $schemaVersion;
        $versendenInfo->getServices()->preferredLocation = $preferredLocation;

        $json = \Dhl\Versenden\Bcs\Api\Info\Serializer::serialize($versendenInfo);
        $unserialized = \Dhl\Versenden\Bcs\Api\Info\Serializer::unserialize($json);
        $this->assertNull($unserialized);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildFromSalesEntityWithServices()
    {
        $countryId = 'DE';
        $streetName = 'Street Name';
        $streetNumber = '127';
        $streetSupplement = 'Address Addition';
        $street = array("$streetName $streetNumber", $streetSupplement);

        $address = Mage::getModel('sales/quote_address');
        $address->setCountryId($countryId);
        $address->setStreet($street);

        $preferredLocation = 'Garage';
        $selectedServices = array(
            Service\PreferredLocation::CODE => Service\PreferredLocation::CODE,
        );
        $serviceDetails = array(
            Service\PreferredLocation::CODE => $preferredLocation,
        );
        $serviceInfo = array(
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        );

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $versendenInfo = $builder->infoFromSales($address, $serviceInfo, 'store_one');
        $this->assertEquals($preferredLocation, $versendenInfo->getServices()->preferredLocation);
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        $this->assertEquals($streetSupplement, $versendenInfo->getReceiver()->addressAddition);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildFromSalesEntityWithPackstation()
    {
        $countryId = 'DE';
        $streetName = 'PackStation';
        $streetNumber = '127';
        $streetSupplement = 'Address Addition';
        $street = array("$streetName $streetNumber", $streetSupplement);

        $address = Mage::getModel('sales/quote_address');
        $address->setCountryId($countryId);
        $address->setStreet($street);

        $selectedServices = array();
        $serviceDetails = array();
        $serviceInfo = array(
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        );

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $versendenInfo = $builder->infoFromSales($address, $serviceInfo, 'store_one');
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        $this->assertEquals($streetSupplement, $versendenInfo->getReceiver()->addressAddition);

        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->getPackstation()->packstationNumber);
        $this->assertNull($versendenInfo->getReceiver()->getPostfiliale()->postfilialNumber);
        $this->assertNull($versendenInfo->getReceiver()->getParcelShop()->parcelShopNumber);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildFromSalesEntityWithPostfiliale()
    {
        $countryId = 'DE';
        $streetName = 'Postfiliale';
        $streetNumber = '127';
        $streetSupplement = 'Address Addition';
        $street = array("$streetName $streetNumber", $streetSupplement);

        $address = Mage::getModel('sales/quote_address');
        $address->setCountryId($countryId);
        $address->setStreet($street);

        $selectedServices = array();
        $serviceDetails = array();
        $serviceInfo = array(
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        );

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $versendenInfo = $builder->infoFromSales($address, $serviceInfo, 'store_one');
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        $this->assertEquals($streetSupplement, $versendenInfo->getReceiver()->addressAddition);

        $this->assertNull($versendenInfo->getReceiver()->getPackstation()->packstationNumber);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->getPostfiliale()->postfilialNumber);
        $this->assertNull($versendenInfo->getReceiver()->getParcelShop()->parcelShopNumber);
    }

    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider()
     * @loadFixture Model_ConfigTest
     * @loadFixture Model_ShipperConfigTest
     *
     * @param \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder $shipmentOrder
     * @param \Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function buildFromRequestData($shipmentOrder, $expectation)
    {
        $builder = new Dhl_Versenden_Model_Info_Builder();
        $versendenInfo = $builder->infoFromRequestData($shipmentOrder);

        $this->assertEquals($expectation->getReceiverStreetName(), $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($expectation->getReceiverStreetNumber(), $versendenInfo->getReceiver()->streetNumber);
        $this->assertEquals($expectation->getReceiverAddressAddition(), $versendenInfo->getReceiver()->addressAddition);
    }
}
