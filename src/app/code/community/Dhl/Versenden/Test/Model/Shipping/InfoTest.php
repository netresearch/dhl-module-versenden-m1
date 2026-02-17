<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

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
        $shippingInfo = \Dhl\Versenden\ParcelDe\Info\Serializer::unserialize($jsonInfo);
        static::assertInstanceOf(
            \Dhl\Versenden\ParcelDe\Info::class,
            $shippingInfo,
        );
        static::assertInstanceOf(
            \Dhl\Versenden\ParcelDe\Info\Services::class,
            $shippingInfo->getServices(),
        );
        static::assertInstanceOf(
            \Dhl\Versenden\ParcelDe\Info\Receiver::class,
            $shippingInfo->getReceiver(),
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
        $shippingInfo = new \Dhl\Versenden\ParcelDe\Info();
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

        $json = \Dhl\Versenden\ParcelDe\Info\Serializer::serialize($shippingInfo);


        // create shipping info from serialized string
        $unserialized = \Dhl\Versenden\ParcelDe\Info\Serializer::unserialize($json);

        static::assertEquals($preferredLocation, $unserialized->getServices()->preferredLocation);
        static::assertNull($unserialized->getServices()->preferredNeighbour);

        static::assertEquals($streetNumber, $unserialized->getReceiver()->streetNumber);
        static::assertEquals($streetName, $unserialized->getReceiver()->streetName);
        static::assertEquals($countryCode, $unserialized->getReceiver()->countryISOCode);

        static::assertEquals($packstationNumber, $unserialized->getReceiver()->getPackstation()->packstationNumber);
        static::assertEquals($postNumber, $unserialized->getReceiver()->getPackstation()->postNumber);
        static::assertEquals($countryCode, $unserialized->getReceiver()->getPackstation()->countryISOCode);

        static::assertEquals($postfilialNumber, $unserialized->getReceiver()->getPostfiliale()->postfilialNumber);
        static::assertEquals($postNumber, $unserialized->getReceiver()->getPostfiliale()->postNumber);
        static::assertEquals($countryCode, $unserialized->getReceiver()->getPostfiliale()->countryISOCode);

        static::assertEquals($parcelShopNumber, $unserialized->getReceiver()->getParcelShop()->parcelShopNumber);
        static::assertEquals($streetName, $unserialized->getReceiver()->getParcelShop()->streetName);
        static::assertEquals($streetNumber, $unserialized->getReceiver()->getParcelShop()->streetNumber);
        static::assertEquals($countryCode, $unserialized->getReceiver()->getParcelShop()->countryISOCode);
    }

    /**
     * @test
     */
    public function updateVersendenInfo()
    {
        // set service #1
        $preferredLocation = 'Foo';
        $versendenInfo = new \Dhl\Versenden\ParcelDe\Info();
        $versendenInfo->getServices()->preferredLocation = $preferredLocation;

        // check if service is included
        $services = $versendenInfo->getServices()->toArray();
        static::assertArrayHasKey('preferred_location', $services);
        static::assertEquals($preferredLocation, $services['preferred_location']);

        // add service #2
        $preferredNeighbour = 'Bar';
        $services['preferred_neighbour'] = $preferredNeighbour;
        $versendenInfo->getServices()->fromArray($services);

        // check if services are included
        static::assertEquals($preferredNeighbour, $versendenInfo->getServices()->preferredNeighbour);
        static::assertEquals($preferredLocation, $versendenInfo->getServices()->preferredLocation);

        // add packstation
        $stationId = '808';
        $streetNumber = '303';
        $postalFacility = [
            'packstation_number' => $stationId,
            'post_number' => '12345678',
        ];
        $receiver = [
            'packstation' => $postalFacility,
        ];
        $versendenInfo->getReceiver()->fromArray($receiver);

        // check if packstation is included
        static::assertSame($stationId, $versendenInfo->getReceiver()->getPackstation()->packstationNumber);

        // add address data
        $receiver = $versendenInfo->getReceiver()->toArray();
        $receiver['street'] = 'Place de la Foo';
        $receiver['street_number'] = $streetNumber;
        $versendenInfo->getReceiver()->fromArray($receiver);

        static::assertSame($stationId, $versendenInfo->getReceiver()->getPackstation()->packstationNumber);
        static::assertSame($streetNumber, $versendenInfo->getReceiver()->streetNumber);
    }

    /**
     * @test
     */
    public function wrongSchemaVersion()
    {
        $versendenInfo = new \Dhl\Versenden\ParcelDe\Info();
        $schemaVersion = 'Foo';
        $preferredLocation = 'Bar';

        $versendenInfo->schemaVersion = $schemaVersion;
        $versendenInfo->getServices()->preferredLocation = $preferredLocation;

        $json = \Dhl\Versenden\ParcelDe\Info\Serializer::serialize($versendenInfo);
        $unserialized = \Dhl\Versenden\ParcelDe\Info\Serializer::unserialize($json);
        static::assertNull($unserialized);
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
        $street = ["$streetName $streetNumber", $streetSupplement];

        $address = Mage::getModel('sales/quote_address');
        $address->setCountryId($countryId);
        $address->setStreet($street);

        $preferredLocation = 'Garage';
        $selectedServices = [
            Service\PreferredLocation::CODE => Service\PreferredLocation::CODE,
        ];
        $serviceDetails = [
            Service\PreferredLocation::CODE => $preferredLocation,
        ];
        $serviceInfo = [
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $versendenInfo = $builder->infoFromSales($address, $serviceInfo, 'store_one');
        static::assertEquals($preferredLocation, $versendenInfo->getServices()->preferredLocation);
        static::assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        static::assertEquals($streetSupplement, $versendenInfo->getReceiver()->addressAddition);
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
        $street = ["$streetName $streetNumber", $streetSupplement];

        $address = Mage::getModel('sales/quote_address');
        $address->setCountryId($countryId);
        $address->setStreet($street);

        $selectedServices = [];
        $serviceDetails = [];
        $serviceInfo = [
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $versendenInfo = $builder->infoFromSales($address, $serviceInfo, 'store_one');
        static::assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        static::assertEquals($streetSupplement, $versendenInfo->getReceiver()->addressAddition);

        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->getPackstation()->packstationNumber);
        static::assertNull($versendenInfo->getReceiver()->getPostfiliale()->postfilialNumber);
        static::assertNull($versendenInfo->getReceiver()->getParcelShop()->parcelShopNumber);
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
        $street = ["$streetName $streetNumber", $streetSupplement];

        $address = Mage::getModel('sales/quote_address');
        $address->setCountryId($countryId);
        $address->setStreet($street);

        $selectedServices = [];
        $serviceDetails = [];
        $serviceInfo = [
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $versendenInfo = $builder->infoFromSales($address, $serviceInfo, 'store_one');
        static::assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        static::assertEquals($streetSupplement, $versendenInfo->getReceiver()->addressAddition);

        static::assertNull($versendenInfo->getReceiver()->getPackstation()->packstationNumber);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->getPostfiliale()->postfilialNumber);
        static::assertNull($versendenInfo->getReceiver()->getParcelShop()->parcelShopNumber);
    }

}
