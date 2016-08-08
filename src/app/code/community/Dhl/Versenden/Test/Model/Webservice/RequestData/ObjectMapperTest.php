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
use \Dhl\Versenden\Webservice\RequestData\ObjectMapper;
use \Dhl\Versenden\Webservice\RequestData\ShippingInfo;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Settings;
/**
 * Dhl_Versenden_Test_Model_Webservice_RequestData_ObjectMapperTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_RequestData_ObjectMapperTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @param $receiverName
     * @param $receiverStreet
     * @param $receiverStreetNumber
     * @param $receiverZip
     * @param $receiverCity
     * @param $receiverCountry
     * @param $stationId
     * @param $postNumber
     * @return Receiver
     */
    protected function getReceiver(
        $receiverName, $receiverStreet, $receiverStreetNumber, $receiverZip,
        $receiverCity, $receiverCountry, $stationId, $postNumber
    ) {
        $packStation = new Receiver\Packstation(
            $receiverZip,
            $receiverCity,
            '',
            $receiverCountry,
            '',
            $stationId,
            $postNumber
        );

        $receiver = new Receiver(
            $receiverName,
            '',
            '',
            $receiverStreet,
            $receiverStreetNumber,
            '',
            '',
            $receiverZip,
            $receiverCity,
            '',
            $receiverCountry,
            '',
            '',
            '',
            '',
            $packStation
        );

        return $receiver;
    }

    /**
     * @param $location
     * @param $age
     * @param $insurance
     * @param $bulkyGoods
     * @return Settings\ServiceSettings
     */
    protected function getServiceSettings($location, $age, $insurance, $bulkyGoods)
    {
        $serviceSettings = Settings\ServiceSettings::fromProperties(
            false,
            false,
            $location,
            false,
            0,
            $age,
            false,
            $insurance,
            $bulkyGoods
        );

        return $serviceSettings;
    }

    /**
     * @param $date
     * @param $reference
     * @param $weight
     * @param $product
     * @return Settings\ShipmentSettings
     */
    protected function getShipmentSettings($date, $reference, $weight, $product)
    {
        $shipmentSettings = new Settings\ShipmentSettings($date, $reference, $weight, $product);

        return $shipmentSettings;
    }

    /**
     * @test
     */
    public function map()
    {
        $receiverName = 'Foo Name';
        $receiverStreet = 'Foo Street';
        $receiverStreetNumber = 'Foo 1';
        $receiverZip = 'Foo Zip';
        $receiverCity = 'Foo City';
        $receiverCountry = 'XX';
        $stationId = '123';
        $postNumber = '123456';

        $receiver = $this->getReceiver(
            $receiverName, $receiverStreet, $receiverStreetNumber, $receiverZip,
            $receiverCity, $receiverCountry, $stationId, $postNumber
        );


        $location = 'Garage';
        $age = 'A18';
        $insurance = 'B';
        $bulkyGoods = true;

        $serviceSettings = $this->getServiceSettings($location, $age, $insurance, $bulkyGoods);


        $date = '1970-01-01';
        $reference = 'XXX';
        $weight = 1.5;
        $product = 'DHL00PAK';

        $shipmentSettings = $this->getShipmentSettings($date, $reference, $weight, $product);

        $shippingInfo = new ShippingInfo($receiver, $serviceSettings, $shipmentSettings);


        $json = json_encode($shippingInfo, JSON_FORCE_OBJECT);
        $stdObject = json_decode($json);

        $shippingInfo = ObjectMapper::getShippingInfo($stdObject);

        $this->assertEquals($shippingInfo->getSchemaVersion(), ObjectMapper::SCHEMA_VERSION);

        $this->assertInstanceOf(Receiver::class, $shippingInfo->getReceiver());
        $this->assertEquals($receiverName, $shippingInfo->getReceiver()->getName1());
        $this->assertEquals($receiverStreet, $shippingInfo->getReceiver()->getStreetName());
        $this->assertEquals($receiverStreetNumber, $shippingInfo->getReceiver()->getStreetNumber());
        $this->assertEquals($receiverZip, $shippingInfo->getReceiver()->getZip());
        $this->assertEquals($receiverCity, $shippingInfo->getReceiver()->getCity());
        $this->assertEquals($receiverCountry, $shippingInfo->getReceiver()->getCountryISOCode());
        $this->assertEquals($stationId, $shippingInfo->getReceiver()->getPackstation()->getPackstationNumber());
        $this->assertEquals($postNumber, $shippingInfo->getReceiver()->getPackstation()->getPostNumber());

        $this->assertEquals($location, $shippingInfo->getServiceSettings()->getPreferredLocation());
        $this->assertEquals($age, $shippingInfo->getServiceSettings()->getVisualCheckOfAge());
        $this->assertEquals($insurance, $shippingInfo->getServiceSettings()->getInsurance());
        $this->assertEquals($bulkyGoods, $shippingInfo->getServiceSettings()->isBulkyGoods());

        $this->assertEquals($date, $shippingInfo->getShipmentSettings()->getDate());
        $this->assertEquals($reference, $shippingInfo->getShipmentSettings()->getReference());
        $this->assertEquals($weight, $shippingInfo->getShipmentSettings()->getWeight());
        $this->assertEquals($product, $shippingInfo->getShipmentSettings()->getProduct());
    }

    /**
     * @test
     */
    public function wrongSchemaVersion()
    {
        $receiverName = 'Foo Name';
        $receiverStreet = 'Foo Street';
        $receiverStreetNumber = 'Foo 1';
        $receiverZip = 'Foo Zip';
        $receiverCity = 'Foo City';
        $receiverCountry = 'XX';
        $stationId = '123';
        $postNumber = '123456';

        $receiver = $this->getReceiver(
            $receiverName, $receiverStreet, $receiverStreetNumber, $receiverZip,
            $receiverCity, $receiverCountry, $stationId, $postNumber
        );


        $location = 'Garage';
        $age = 'A18';
        $insurance = 'B';
        $bulkyGoods = true;

        $serviceSettings = $this->getServiceSettings($location, $age, $insurance, $bulkyGoods);


        $date = '1970-01-01';
        $reference = 'XXX';
        $weight = 1.5;
        $product = 'DHL00PAK';

        $shipmentSettings = $this->getShipmentSettings($date, $reference, $weight, $product);

        $shippingInfo = new ShippingInfo($receiver, $serviceSettings, $shipmentSettings);


        $json = json_encode($shippingInfo, JSON_FORCE_OBJECT);
        $stdObject = json_decode($json);
        $stdObject->schemaVersion = '0.99';
        $shippingInfo = ObjectMapper::getShippingInfo($stdObject);

        $this->assertNull($shippingInfo);
    }
}
