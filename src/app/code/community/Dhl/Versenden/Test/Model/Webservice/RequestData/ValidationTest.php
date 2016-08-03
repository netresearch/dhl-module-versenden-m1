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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;
/**
 * Dhl_Versenden_Test_Model_Webservice_RequestData_ValidationTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_RequestData_ValidationTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\RequestDataException
     */
    public function requiredValue()
    {
        // name is required
        $name = '';

        $streetName = 'Foo Street Name';
        $streetNumber = 'Foo 1';
        $postalCode = 'Foo Zip';
        $city = 'Foo City';
        $countryIsoCode = 'XX';

        new Receiver(
            $name,
            '',
            '',
            $streetName,
            $streetNumber,
            '',
            '',
            $postalCode,
            $city,
            '',
            $countryIsoCode,
            '',
            '',
            '',
            ''
        );
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\RequestDataException
     */
    public function tooShort()
    {
        $name = 'Foo Name';
        $streetName = 'Foo Street Name';
        $streetNumber = 'Foo 1';
        $postalCode = 'Foo Zip';
        $city = 'Foo City';

        // country too short
        $countryIsoCode = 'X';

        new Receiver(
            $name,
            '',
            '',
            $streetName,
            $streetNumber,
            '',
            '',
            $postalCode,
            $city,
            '',
            $countryIsoCode,
            '',
            '',
            '',
            ''
        );
    }

    /**
     * @test
     * @expectedException \Dhl\Versenden\Webservice\RequestDataException
     */
    public function tooLong()
    {
        $name = 'Foo Name';
        $streetName = 'Foo Street Name';

        // street number too long
        $streetNumber = 'Foo 12345a';

        $postalCode = 'Foo Zip';
        $city = 'Foo City';
        $countryIsoCode = 'XX';

        new Receiver(
            $name,
            '',
            '',
            $streetName,
            $streetNumber,
            '',
            '',
            $postalCode,
            $city,
            '',
            $countryIsoCode,
            '',
            '',
            '',
            ''
        );
    }

    /**
     * @test
     */
    public function validationOk()
    {
        $name = 'Foo Name';
        $streetName = 'Foo Street Name';
        $streetNumber = 'Foo 1';
        $postalCode = 'Foo Zip';
        $city = 'Foo City';
        $countryIsoCode = 'XX';

        $receiver = new Receiver(
            $name,
            '',
            '',
            $streetName,
            $streetNumber,
            '',
            '',
            $postalCode,
            $city,
            '',
            $countryIsoCode,
            '',
            '',
            '',
            ''
        );

        $this->assertEquals($name, $receiver->getName1());
        $this->assertEquals($streetName, $receiver->getStreetName());
        $this->assertEquals($streetNumber, $receiver->getStreetNumber());
        $this->assertEquals($postalCode, $receiver->getZip());
        $this->assertEquals($city, $receiver->getCity());
        $this->assertEquals($countryIsoCode, $receiver->getCountryISOCode());
    }
}
