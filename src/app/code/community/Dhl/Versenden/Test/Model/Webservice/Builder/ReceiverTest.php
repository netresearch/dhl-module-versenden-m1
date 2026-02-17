<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_ReceiverTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function constructorArgCountryDirectoryMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Receiver(
            [
                'helper' => Mage::helper('dhl_versenden/data'),
            ],
        );
    }

    /**
     * @test
     */
    public function constructorArgCountryDirectoryWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Receiver(
            [
                'country_directory' => new stdClass(),
                'helper' => Mage::helper('dhl_versenden/data'),
            ],
        );
    }

    /**
     * @test
     */
    public function constructorArgHelperMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = [
            'country_directory' => Mage::getModel('directory/country'),
        ];
        new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);
    }

    /**
     * @test
     */
    public function constructorArgHelperWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => new stdClass(),
        ];
        new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);
    }

    /**
     * @test
     */
    public function build()
    {
        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address'),
        ];
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
        $country = 'DE'; // ISO-2 input (what address stores)
        $countryISO3 = 'DEU'; // ISO-3 output (what SDK receives after transformation)
        $telephone = '54321';
        $email = 'a@b.c';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(['getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',])
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

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setRecipientAddress'])
            ->getMock();

        $sdkBuilder->expects(static::once())
            ->method('setRecipientAddress')
            ->with(
                $name,
                $countryISO3,
                $postCode,
                $city,
                $streetName,
                $streetNumber,
                $company,
                null,
                $email,
                '',
                null,
                null,
                null,
                [],
            );

        $builder->build($sdkBuilder, $address);
    }

    /**
     * @test
     */
    public function packStationAddressToBuild()
    {
        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address'),
        ];

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Packstation';
        $streetNumber = '111';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE'; // ISO-2 input (what address stores)
        $countryISO3 = 'DEU'; // ISO-3 output (what SDK receives after transformation)
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '123456';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(['getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',])
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

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setPackstation'])
            ->getMock();

        $sdkBuilder->expects(static::once())
            ->method('setPackstation')
            ->with(
                $name,
                $postNumber,
                $streetNumber,
                $countryISO3,
                $postCode,
                $city,
                null,
                null,
            );

        $builder->build($sdkBuilder, $address);
    }
    /**
     * @test
     */
    public function postOfficeAddressToBuild()
    {
        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address'),
        ];
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Postfiliale';
        $streetNumber = '888';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE'; // ISO-2 input (what address stores)
        $countryISO3 = 'DEU'; // ISO-3 output (what SDK receives after transformation)
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '654321';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(['getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',])
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

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setPostfiliale'])
            ->getMock();

        $sdkBuilder->expects(static::once())
            ->method('setPostfiliale')
            ->with(
                $name,
                $streetNumber,
                $countryISO3,
                $postCode,
                $city,
                null,
                $postNumber,
                null,
                null,
            );

        $builder->build($sdkBuilder, $address);
    }

    /**
     * @test
     */
    public function parcelShopAddressToBuild()
    {
        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address'),
        ];
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver($args);

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Paketshop';
        $streetNumber = '999';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE'; // ISO-2 input (what address stores)
        $countryISO3 = 'DEU'; // ISO-3 output (what SDK receives after transformation)
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '654321';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(['getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',])
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

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setRecipientAddress'])
            ->getMock();

        // parcel shops fall back to regular recipient address
        $sdkBuilder->expects(static::once())
            ->method('setRecipientAddress')
            ->with(
                $name,
                $countryISO3,
                $postCode,
                $city,
                $streetName,
                $streetNumber,
                $postNumber,
                null,
                $email,
                '',
                null,
                null,
                null,
                [],
            );

        $builder->build($sdkBuilder, $address);
    }

    /**
     * Test that email is omitted when parcel announcement is disabled.
     *
     * @test
     */
    public function buildOmitsEmailWhenNotIncluded()
    {
        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address'),
        ];
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
        $countryISO3 = 'DEU';
        $telephone = '54321';
        $email = 'a@b.c';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(['getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',])
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

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setRecipientAddress'])
            ->getMock();

        // Email should be null when includeRecipientEmail is false
        $sdkBuilder->expects(static::once())
            ->method('setRecipientAddress')
            ->with(
                $name,
                $countryISO3,
                $postCode,
                $city,
                $streetName,
                $streetNumber,
                $company,
                null,
                null, // email omitted
                '',
                null,
                null,
                null,
                [],
            );

        $builder->build($sdkBuilder, $address, false);
    }

    /**
     * Test that phone number is included when config allows it.
     *
     * @test
     */
    public function buildIncludesPhoneWhenConfigEnabled()
    {
        // Mock config to return true for isSendReceiverPhone
        $configMock = $this->getModelMock('dhl_versenden/config', ['isSendReceiverPhone']);
        $configMock->method('isSendReceiverPhone')->willReturn(true);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/address'),
        ];
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
        $countryISO3 = 'DEU';
        $telephone = '54321';
        $email = 'a@b.c';

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(['getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',])
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

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setRecipientAddress'])
            ->getMock();

        // Phone should be included when config allows
        $sdkBuilder->expects(static::once())
            ->method('setRecipientAddress')
            ->with(
                $name,
                $countryISO3,
                $postCode,
                $city,
                $streetName,
                $streetNumber,
                $company,
                null,
                $email,
                $telephone, // Phone should be passed
                null,
                null,
                null,
                [],
            );

        $builder->build($sdkBuilder, $address);
    }
}
