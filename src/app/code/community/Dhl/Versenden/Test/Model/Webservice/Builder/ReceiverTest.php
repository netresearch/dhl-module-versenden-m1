<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_ReceiverTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgCountryDirectoryMissing()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Receiver(
            array(
                'helper' => Mage::helper('dhl_versenden/data')
            )
        );
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgCountryDirectoryWrongType()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Receiver(
            array(
                'country_directory' => new stdClass(),
                'helper' => Mage::helper('dhl_versenden/data')
            )
        );
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgHelperMissing()
    {
        $args = array(
            'country_directory' => Mage::getModel('directory/country'),
        );
        new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgHelperWrongType()
    {
        $args = array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => new stdClass()
        );
        new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);
    }

    /**
     * @test
     */
    public function getReceiver()
    {
        $args = array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address')
        );
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $company = 'Foo Inc.';
        $streetName = 'Xx';
        $streetNumber = '111';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(array('getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',))
            ->getMock();
        $address->method('getOrder')->willReturn($order);
        $address->method('getFirstname')->willReturn($firstName);
        $address->method('getLastname')->willReturn($lastName);
        $address->method('getCompany')->willReturn($company);
        $address->method('getStreetFull')->willReturn($streetFull);
        $address->method('getPostcode')->willReturn($postCode);
        $address->method('getCity')->willReturn($city);
        $address->method('getCountryId')->willReturn($country);
        $address->method('getTelephone')->willReturn($telephone);
        $address->method('getEmail')->willReturn($email);

        $receiver = $builder->getReceiver($address);
        $this::assertSame($name, $receiver->getName1(), 'First name does not match');
        $this::assertSame($company, $receiver->getName2(), 'Last name does not match');
        $this::assertSame($streetName, $receiver->getStreetName(), 'Street name does not match');
        $this::assertSame($streetNumber, $receiver->getStreetNumber(), 'Street number does not match');
        $this::assertSame($postCode, $receiver->getZip(), 'ZIP code does not match');
        $this::assertSame($city, $receiver->getCity(), 'City does not match');
        $this::assertSame($country, $receiver->getCountryISOCode(), 'Country ISO does not match');
        /** Phone number is only set when Config::SENDRECEIVERPHONE is true. */
        $this::assertEmpty($receiver->getPhone(), 'Phone should not be set by default');
        $this::assertSame($email, $receiver->getEmail(), 'Email does not match');
        $this::assertNull($receiver->getPackstation(), 'Packstation is not null');
        $this::assertNull($receiver->getPostfiliale(), 'Postfiliale is not null');
        $this::assertNull($receiver->getParcelShop(), 'Parcel shop is not null');
    }

    /**
     * @test
     */
    public function packStationAddressToReceiver()
    {
        $args = array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address')
        );

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Packstation';
        $streetNumber = '111';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '123456';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(array('getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',))
            ->getMock();
        $address->method('getOrder')->willReturn($order);
        $address->method('getFirstname')->willReturn($firstName);
        $address->method('getLastname')->willReturn($lastName);
        $address->method('getCompany')->willReturn($postNumber);
        $address->method('getStreetFull')->willReturn($streetFull);
        $address->method('getPostcode')->willReturn($postCode);
        $address->method('getCity')->willReturn($city);
        $address->method('getCountryId')->willReturn($country);
        $address->method('getTelephone')->willReturn($telephone);
        $address->method('getEmail')->willReturn($email);

        $receiver = $builder->getReceiver($address);
        $this::assertSame($name, $receiver->getName1());
        $this::assertNotNull($receiver->getPackstation());
        $this::assertSame($streetNumber, $receiver->getPackstation()->getPackstationNumber());
        $this::assertSame($postNumber, $receiver->getPackstation()->getPostNumber());
        $this::assertSame($postCode, $receiver->getPackstation()->getZip());
        $this::assertSame($city, $receiver->getPackstation()->getCity());
        $this::assertNull($receiver->getPostfiliale());
        $this::assertNull($receiver->getParcelShop());
    }
    /**
     * @test
     */
    public function postOfficeAddressToReceiver()
    {
        $args = array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address')
        );
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Postfiliale';
        $streetNumber = '888';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '654321';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(array('getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',))
            ->getMock();
        $address->method('getOrder')->willReturn($order);
        $address->method('getFirstname')->willReturn($firstName);
        $address->method('getLastname')->willReturn($lastName);
        $address->method('getCompany')->willReturn($postNumber);
        $address->method('getStreetFull')->willReturn($streetFull);
        $address->method('getPostcode')->willReturn($postCode);
        $address->method('getCity')->willReturn($city);
        $address->method('getCountryId')->willReturn($country);
        $address->method('getTelephone')->willReturn($telephone);
        $address->method('getEmail')->willReturn($email);

        $receiver = $builder->getReceiver($address);
        $this::assertSame($name, $receiver->getName1());
        $this::assertNotNull($receiver->getPostfiliale());
        $this::assertSame($streetNumber, $receiver->getPostfiliale()->getPostfilialNumber());
        $this::assertSame($postNumber, $receiver->getPostfiliale()->getPostNumber());
        $this::assertSame($postCode, $receiver->getPostfiliale()->getZip());
        $this::assertSame($city, $receiver->getPostfiliale()->getCity());
        $this::assertNull($receiver->getPackstation());
        $this::assertNull($receiver->getParcelShop());
    }

    /**
     * @test
     */
    public function parcelShopAddressToReceiver()
    {
        $args = array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address')
        );
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Paketshop';
        $streetNumber = '999';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '654321';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(array('getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',))
            ->getMock();
        $address->method('getOrder')->willReturn($order);
        $address->method('getFirstname')->willReturn($firstName);
        $address->method('getLastname')->willReturn($lastName);
        $address->method('getCompany')->willReturn($postNumber);
        $address->method('getStreetFull')->willReturn($streetFull);
        $address->method('getPostcode')->willReturn($postCode);
        $address->method('getCity')->willReturn($city);
        $address->method('getCountryId')->willReturn($country);
        $address->method('getTelephone')->willReturn($telephone);
        $address->method('getEmail')->willReturn($email);

        // parcel shops are not handled by this extension
        $receiver = $builder->getReceiver($address);
        $this::assertSame($name, $receiver->getName1());
        $this::assertNull($receiver->getParcelShop());
        $this::assertNull($receiver->getPackstation());
        $this::assertNull($receiver->getPostfiliale());
    }
}
