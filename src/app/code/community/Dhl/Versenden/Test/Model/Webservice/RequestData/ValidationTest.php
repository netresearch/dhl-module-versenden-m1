<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

class Dhl_Versenden_Test_Model_Webservice_RequestData_ValidationTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @expectedException \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ValidationException
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
     * @expectedException \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ValidationException
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
     * @expectedException \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ValidationException
     */
    public function tooLong()
    {
        $name = 'Foo Name';
        $streetName = 'Foo Street Name';

        // house number too long
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
