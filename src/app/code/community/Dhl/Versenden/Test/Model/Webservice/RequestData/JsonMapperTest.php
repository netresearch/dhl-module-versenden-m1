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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Settings;
/**
 * Dhl_Versenden_Test_Model_Webservice_RequestData_JsonMapperTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_RequestData_JsonMapperTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function mapReceiver()
    {
        $stationId = '123';
        $receiverName = 'Foo';

        $packStation = new Receiver\Packstation('04229', 'Leipzig', 'Germany', 'DE', '', $stationId, '123456');
        $receiver = new Receiver(
            $receiverName,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $packStation
        );

        $json = json_encode($receiver, JSON_FORCE_OBJECT);
        $stdObject = json_decode($json);

        $receiver = ObjectMapper::getReceiver($stdObject);

        $this->assertInstanceOf(Receiver::class, $receiver);
        $this->assertEquals($stationId, $receiver->getPackstation()->getPackstationNumber());
        $this->assertEquals($receiverName, $receiver->getName1());
    }

    /**
     * @test
     */
    public function mapServiceSettings()
    {
        $location = 'Garage';
        $age = 'A18';
        $insurance = 'B';
        $bulkyGoods = true;
        $serviceSettings = new Settings\ServiceSettings(
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

        $json = json_encode($serviceSettings, JSON_FORCE_OBJECT);
        $stdObject = json_decode($json);

        $serviceSettings = ObjectMapper::getServiceSettings($stdObject);
        $this->assertInstanceOf(Settings\ServiceSettings::class, $serviceSettings);
        $this->assertEquals($location, $serviceSettings->getPreferredLocation());
        $this->assertEquals($age, $serviceSettings->getVisualCheckOfAge());
        $this->assertEquals($insurance, $serviceSettings->getInsurance());
        $this->assertEquals($bulkyGoods, $serviceSettings->isBulkyGoods());
    }

    public function mapShipmentSettings()
    {
        
    }
}
