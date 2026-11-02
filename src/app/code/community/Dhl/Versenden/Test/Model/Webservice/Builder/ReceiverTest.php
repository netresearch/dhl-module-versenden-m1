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

    /**
     * @return mixed[]
     */
    public function regionDataProvider()
    {
        $mecklenburgId = Mage::getModel('directory/region')->loadByCode('MEC', 'DE')->getId();

        return [
            'region from directory is sent as code' => ['DE', ['region_id' => $mecklenburgId], 'MEC'],
            'short free-text region is kept' => ['GB', ['region' => 'Kent'], 'Kent'],
            'free-text region above API limit is omitted' => ['GB', ['region' => 'Cornwall and Isles of Scilly'], null],
            'empty region is omitted' => ['DE', ['region' => ''], null],
            'missing region is omitted' => ['DE', [], null],
        ];
    }

    /**
     * The REST API limits the receiver state to 1-20 characters.
     *
     * @test
     * @dataProvider regionDataProvider
     */
    public function buildSendsRegionCodeForRecipientAddress($countryId, array $regionData, $expectedState)
    {
        $arguments = $this->captureSdkBuilderArguments('setRecipientAddress', $countryId, $regionData, 'Xx 111');

        static::assertSame($expectedState, $arguments[11]);
    }

    /**
     * @test
     * @dataProvider regionDataProvider
     */
    public function buildSendsRegionCodeForPackstation($countryId, array $regionData, $expectedState)
    {
        $arguments = $this->captureSdkBuilderArguments('setPackstation', $countryId, $regionData, 'Packstation 111');

        static::assertSame($expectedState, $arguments[6]);
    }

    /**
     * @test
     * @dataProvider regionDataProvider
     */
    public function buildSendsRegionCodeForPostfiliale($countryId, array $regionData, $expectedState)
    {
        $arguments = $this->captureSdkBuilderArguments('setPostfiliale', $countryId, $regionData, 'Postfiliale 888');

        static::assertSame($expectedState, $arguments[7]);
    }

    /**
     * Run the receiver builder on an address with real region handling and
     * return the arguments passed to the given SDK builder method.
     *
     * @param string $sdkMethod
     * @param string $countryId
     * @param mixed[] $regionData
     * @param string $streetFull
     * @return mixed[]
     */
    private function captureSdkBuilderArguments($sdkMethod, $countryId, array $regionData, $streetFull)
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver(
            [
                'country_directory' => Mage::getModel('directory/country'),
                'helper' => Mage::helper('dhl_versenden/address'),
            ],
        );

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(0);
        /** @var Mage_Sales_Model_Quote_Address|PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->setMethods(['getOrder', 'getFirstname', 'getLastname', 'getCompany', 'getStreetFull', 'getPostcode', 'getCity', 'getCountryId', 'getTelephone', 'getEmail',])
            ->getMock();
        $address->method('getOrder')->willReturn($order);
        $address->method('getFirstname')->willReturn('Foo');
        $address->method('getLastname')->willReturn('Bar');
        $address->method('getCompany')->willReturn('654321');
        $address->method('getStreetFull')->willReturn($streetFull);
        $address->method('getPostcode')->willReturn('12345');
        $address->method('getCity')->willReturn('Foo');
        $address->method('getCountryId')->willReturn($countryId);
        $address->method('getTelephone')->willReturn('54321');
        $address->method('getEmail')->willReturn('a@b.c');
        $address->addData($regionData);

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods([$sdkMethod])
            ->getMock();

        $arguments = null;
        $sdkBuilder->expects(static::once())
            ->method($sdkMethod)
            ->willReturnCallback(
                function () use (&$arguments, $sdkBuilder) {
                    $arguments = func_get_args();
                    return $sdkBuilder;
                },
            );

        $builder->build($sdkBuilder, $address);

        return $arguments;
    }
}
