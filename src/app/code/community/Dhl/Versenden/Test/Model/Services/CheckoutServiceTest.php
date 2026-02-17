<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Test CheckoutService functionality.
 *
 * Tests the checkout service that provides DHL service availability
 * information for the checkout process.
 */
class Dhl_Versenden_Test_Model_Services_CheckoutServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test getPreferredDay returns API response.
     *
     * @test
     */
    public function getPreferredDayReturnsApiResponse()
    {
        // Mock the API response
        $preferredDayMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\PreferredDayAvailable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $availableServicesMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\AvailableServicesMap::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPreferredDay'])
            ->getMock();
        $availableServicesMock->method('getPreferredDay')->willReturn($preferredDayMock);

        // Mock the client
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_parcelManagement',
            ['checkoutRecipientZipAvailableServicesGet'],
        );
        $clientMock->expects(static::once())
            ->method('checkoutRecipientZipAvailableServicesGet')
            ->with(static::isType('string'), '04229')
            ->willReturn($availableServicesMock);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_parcelManagement', $clientMock);

        // Mock quote with shipping address
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode'])
            ->getMock();
        $addressMock->method('getPostcode')->willReturn('04229');

        $storeMock = $this->getMockBuilder(Mage_Core_Model_Store::class)
            ->setMethods(['getId'])
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStore'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getStore')->willReturn($storeMock);

        // Mock config
        $configMock = $this->getModelMock('dhl_versenden/config', ['getExcludedDropOffDays']);
        $configMock->method('getExcludedDropOffDays')->willReturn('');
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        // Mock service config
        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getCutOffTime']);
        $serviceConfigMock->method('getCutOffTime')->willReturn('12,00,00');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $service = new Dhl_Versenden_Model_Services_CheckoutService(['quote' => $quoteMock]);
        $result = $service->getPreferredDay();

        static::assertSame($preferredDayMock, $result);
    }

    /**
     * Test getPreferredLocation returns API response.
     *
     * @test
     */
    public function getPreferredLocationReturnsApiResponse()
    {
        // Mock the API response
        $preferredLocationMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\ServiceAvailable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $availableServicesMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\AvailableServicesMap::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPreferredLocation'])
            ->getMock();
        $availableServicesMock->method('getPreferredLocation')->willReturn($preferredLocationMock);

        // Mock the client
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_parcelManagement',
            ['checkoutRecipientZipAvailableServicesGet'],
        );
        $clientMock->expects(static::once())
            ->method('checkoutRecipientZipAvailableServicesGet')
            ->with(static::isType('string'), '04229')
            ->willReturn($availableServicesMock);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_parcelManagement', $clientMock);

        // Mock quote with shipping address
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode'])
            ->getMock();
        $addressMock->method('getPostcode')->willReturn('04229');

        $storeMock = $this->getMockBuilder(Mage_Core_Model_Store::class)
            ->setMethods(['getId'])
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStore'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getStore')->willReturn($storeMock);

        // Mock config
        $configMock = $this->getModelMock('dhl_versenden/config', ['getExcludedDropOffDays']);
        $configMock->method('getExcludedDropOffDays')->willReturn('');
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        // Mock service config
        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getCutOffTime']);
        $serviceConfigMock->method('getCutOffTime')->willReturn('12,00,00');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $service = new Dhl_Versenden_Model_Services_CheckoutService(['quote' => $quoteMock]);
        $result = $service->getPreferredLocation();

        static::assertSame($preferredLocationMock, $result);
    }

    /**
     * Test getPreferredNeighbour returns API response.
     *
     * @test
     */
    public function getPreferredNeighbourReturnsApiResponse()
    {
        // Mock the API response
        $preferredNeighbourMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\ServiceAvailable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $availableServicesMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\AvailableServicesMap::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPreferredNeighbour'])
            ->getMock();
        $availableServicesMock->method('getPreferredNeighbour')->willReturn($preferredNeighbourMock);

        // Mock the client
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_parcelManagement',
            ['checkoutRecipientZipAvailableServicesGet'],
        );
        $clientMock->expects(static::once())
            ->method('checkoutRecipientZipAvailableServicesGet')
            ->with(static::isType('string'), '04229')
            ->willReturn($availableServicesMock);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_parcelManagement', $clientMock);

        // Mock quote with shipping address
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode'])
            ->getMock();
        $addressMock->method('getPostcode')->willReturn('04229');

        $storeMock = $this->getMockBuilder(Mage_Core_Model_Store::class)
            ->setMethods(['getId'])
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStore'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getStore')->willReturn($storeMock);

        // Mock config
        $configMock = $this->getModelMock('dhl_versenden/config', ['getExcludedDropOffDays']);
        $configMock->method('getExcludedDropOffDays')->willReturn('');
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        // Mock service config
        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getCutOffTime']);
        $serviceConfigMock->method('getCutOffTime')->willReturn('12,00,00');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $service = new Dhl_Versenden_Model_Services_CheckoutService(['quote' => $quoteMock]);
        $result = $service->getPreferredNeighbour();

        static::assertSame($preferredNeighbourMock, $result);
    }

    /**
     * Test getNoNeighbourDelivery returns API response.
     *
     * @test
     */
    public function getNoNeighbourDeliveryReturnsApiResponse()
    {
        // Mock the API response
        $noNeighbourDeliveryMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\ServiceAvailable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $availableServicesMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\AvailableServicesMap::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNoNeighbourDelivery'])
            ->getMock();
        $availableServicesMock->method('getNoNeighbourDelivery')->willReturn($noNeighbourDeliveryMock);

        // Mock the client
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_parcelManagement',
            ['checkoutRecipientZipAvailableServicesGet'],
        );
        $clientMock->expects(static::once())
            ->method('checkoutRecipientZipAvailableServicesGet')
            ->with(static::isType('string'), '04229')
            ->willReturn($availableServicesMock);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_parcelManagement', $clientMock);

        // Mock quote with shipping address
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode'])
            ->getMock();
        $addressMock->method('getPostcode')->willReturn('04229');

        $storeMock = $this->getMockBuilder(Mage_Core_Model_Store::class)
            ->setMethods(['getId'])
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStore'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getStore')->willReturn($storeMock);

        // Mock config
        $configMock = $this->getModelMock('dhl_versenden/config', ['getExcludedDropOffDays']);
        $configMock->method('getExcludedDropOffDays')->willReturn('');
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        // Mock service config
        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getCutOffTime']);
        $serviceConfigMock->method('getCutOffTime')->willReturn('12,00,00');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $service = new Dhl_Versenden_Model_Services_CheckoutService(['quote' => $quoteMock]);
        $result = $service->getNoNeighbourDelivery();

        static::assertSame($noNeighbourDeliveryMock, $result);
    }

    /**
     * Test getService returns false for unknown service.
     *
     * @test
     */
    public function getServiceReturnsFalseForUnknownService()
    {
        // Mock quote with shipping address
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode'])
            ->getMock();
        $addressMock->method('getPostcode')->willReturn('04229');

        $storeMock = $this->getMockBuilder(Mage_Core_Model_Store::class)
            ->setMethods(['getId'])
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStore'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getStore')->willReturn($storeMock);

        $service = new Dhl_Versenden_Model_Services_CheckoutService(['quote' => $quoteMock]);
        $result = $service->getService('unknownService');

        static::assertFalse($result);
    }

    /**
     * Test getService calls correct method for known service.
     *
     * @test
     */
    public function getServiceCallsCorrectMethodForKnownService()
    {
        // Mock the API response
        $preferredDayMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\PreferredDayAvailable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $availableServicesMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\AvailableServicesMap::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPreferredDay'])
            ->getMock();
        $availableServicesMock->method('getPreferredDay')->willReturn($preferredDayMock);

        // Mock the client
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_parcelManagement',
            ['checkoutRecipientZipAvailableServicesGet'],
        );
        $clientMock->expects(static::once())
            ->method('checkoutRecipientZipAvailableServicesGet')
            ->with(static::isType('string'), '04229')
            ->willReturn($availableServicesMock);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_parcelManagement', $clientMock);

        // Mock quote with shipping address
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode'])
            ->getMock();
        $addressMock->method('getPostcode')->willReturn('04229');

        $storeMock = $this->getMockBuilder(Mage_Core_Model_Store::class)
            ->setMethods(['getId'])
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStore'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getStore')->willReturn($storeMock);

        // Mock config
        $configMock = $this->getModelMock('dhl_versenden/config', ['getExcludedDropOffDays']);
        $configMock->method('getExcludedDropOffDays')->willReturn('');
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        // Mock service config
        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getCutOffTime']);
        $serviceConfigMock->method('getCutOffTime')->willReturn('12,00,00');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $service = new Dhl_Versenden_Model_Services_CheckoutService(['quote' => $quoteMock]);
        $result = $service->getService('preferredDay');

        static::assertSame($preferredDayMock, $result);
    }

    /**
     * Test constructor without quote parameter.
     *
     * @test
     */
    public function constructorWithoutQuoteParameter()
    {
        $service = new Dhl_Versenden_Model_Services_CheckoutService([]);

        // Service should be created without error
        static::assertInstanceOf(Dhl_Versenden_Model_Services_CheckoutService::class, $service);
    }

    /**
     * Test API response is cached.
     *
     * @test
     */
    public function apiResponseIsCached()
    {
        // Mock the API response
        $availableServicesMock = $this->getMockBuilder(\Dhl\Versenden\Cig\Model\AvailableServicesMap::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the client - should only be called ONCE due to caching
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_parcelManagement',
            ['checkoutRecipientZipAvailableServicesGet'],
        );
        $clientMock->expects(static::once())
            ->method('checkoutRecipientZipAvailableServicesGet')
            ->willReturn($availableServicesMock);
        $this->replaceByMock('model', 'dhl_versenden/webservice_client_parcelManagement', $clientMock);

        // Mock quote with shipping address
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode'])
            ->getMock();
        $addressMock->method('getPostcode')->willReturn('04229');

        $storeMock = $this->getMockBuilder(Mage_Core_Model_Store::class)
            ->setMethods(['getId'])
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStore'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getStore')->willReturn($storeMock);

        // Mock config
        $configMock = $this->getModelMock('dhl_versenden/config', ['getExcludedDropOffDays']);
        $configMock->method('getExcludedDropOffDays')->willReturn('');
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        // Mock service config
        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getCutOffTime']);
        $serviceConfigMock->method('getCutOffTime')->willReturn('12,00,00');
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        $service = new Dhl_Versenden_Model_Services_CheckoutService(['quote' => $quoteMock]);

        // Call twice - client should only be called once
        $result1 = $service->getRecipientZipAvailableServices();
        $result2 = $service->getRecipientZipAvailableServices();

        static::assertSame($result1, $result2);
    }
}
