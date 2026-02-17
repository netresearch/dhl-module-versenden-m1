<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\Data\Shipper\Contact;

class Dhl_Versenden_Test_Model_Webservice_RequestData_ValidationTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function requiredValue()
    {
        $this->expectException(\Dhl\Versenden\ParcelDe\Config\ValidationException::class);

        // name is required
        $name = '';

        $streetName = 'Foo Street Name';
        $streetNumber = 'Foo 1';
        $postalCode = 'Foo Zip';
        $city = 'Foo City';
        $countryIsoCode = 'XX';

        new Contact(
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
            '',
        );
    }

    /**
     * @test
     */
    public function tooShort()
    {
        $this->expectException(\Dhl\Versenden\ParcelDe\Config\ValidationException::class);

        $name = 'Foo Name';
        $streetName = 'Foo Street Name';
        $streetNumber = 'Foo 1';
        $postalCode = 'Foo Zip';
        $city = 'Foo City';

        // country too short
        $countryIsoCode = 'X';

        new Contact(
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
            '',
        );
    }

    /**
     * @test
     */
    public function tooLong()
    {
        $this->expectException(\Dhl\Versenden\ParcelDe\Config\ValidationException::class);

        $name = 'Foo Name';
        $streetName = 'Foo Street Name';

        // house number too long
        $streetNumber = 'Foo 12345a';

        $postalCode = 'Foo Zip';
        $city = 'Foo City';
        $countryIsoCode = 'XX';

        new Contact(
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
            '',
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

        $contact = new Contact(
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
            '',
        );

        static::assertEquals($name, $contact->getName1());
        static::assertEquals($streetName, $contact->getStreetName());
        static::assertEquals($streetNumber, $contact->getStreetNumber());
        static::assertEquals($postalCode, $contact->getZip());
        static::assertEquals($city, $contact->getCity());
        static::assertEquals($countryIsoCode, $contact->getCountryISOCode());
    }
}
