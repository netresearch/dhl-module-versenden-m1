<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Info_BuilderTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithBasicAddress()
    {
        $this->setCurrentStore('store_one');

        // Create a shipping address with required data
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Max');
        $shippingAddress->setLastname('Mustermann');
        $shippingAddress->setCompany('Test Company');
        $shippingAddress->setStreet(['Teststraße 12']);
        $shippingAddress->setPostcode('04229');
        $shippingAddress->setCity('Leipzig');
        $shippingAddress->setCountryId('DE');
        $shippingAddress->setRegion('Sachsen');
        $shippingAddress->setTelephone('+49 341 123456');
        $shippingAddress->setEmail('test@example.com');

        // Service info with selected services
        $serviceInfo = [
            'shipment_service' => [
                'preferredLocation' => '1',
            ],
            'service_setting' => [
                'preferredLocation' => 'Garage',
            ],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        // Verify result is Info object
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);

        // Verify receiver info
        $receiver = $result->getReceiver();
        static::assertEquals('Max Mustermann', $receiver->name1);
        static::assertEquals('Test Company', $receiver->name2);
        static::assertEquals('04229', $receiver->zip);
        static::assertEquals('Leipzig', $receiver->city);
        static::assertEquals('DE', $receiver->countryISOCode);
        static::assertEquals('+49 341 123456', $receiver->phone);
        static::assertEquals('test@example.com', $receiver->email);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithOrderAddress()
    {
        $this->setCurrentStore('store_one');

        // Create an order address (different class but same parent)
        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setFirstname('Erika');
        $shippingAddress->setLastname('Musterfrau');
        $shippingAddress->setStreet(['Hauptstraße 1']);
        $shippingAddress->setPostcode('10115');
        $shippingAddress->setCity('Berlin');
        $shippingAddress->setCountryId('DE');
        $shippingAddress->setTelephone('+49 30 123456');
        $shippingAddress->setEmail('erika@example.com');

        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);
        static::assertEquals('Erika Musterfrau', $result->getReceiver()->name1);
        static::assertEquals('10115', $result->getReceiver()->zip);
        static::assertEquals('Berlin', $result->getReceiver()->city);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithMultipleServices()
    {
        $this->setCurrentStore('store_one');

        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Test');
        $shippingAddress->setLastname('User');
        $shippingAddress->setStreet(['Am Markt 5']);
        $shippingAddress->setPostcode('01067');
        $shippingAddress->setCity('Dresden');
        $shippingAddress->setCountryId('DE');
        $shippingAddress->setEmail('multi@example.com');

        // Select multiple services
        $serviceInfo = [
            'shipment_service' => [
                'preferredLocation' => '1',
                'preferredNeighbour' => '1',
            ],
            'service_setting' => [
                'preferredLocation' => 'Behind the house',
                'preferredNeighbour' => 'Mr. Schmidt',
            ],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);

        // Services should be populated
        $services = $result->getServices();
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info\Services::class, $services);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithEmptyServices()
    {
        $this->setCurrentStore('store_one');

        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Empty');
        $shippingAddress->setLastname('Services');
        $shippingAddress->setStreet(['Ringstraße 100']);
        $shippingAddress->setPostcode('80331');
        $shippingAddress->setCity('München');
        $shippingAddress->setCountryId('DE');

        // Empty services
        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);
        static::assertEquals('Empty Services', $result->getReceiver()->name1);
        static::assertEquals('München', $result->getReceiver()->city);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithDifferentStore()
    {
        // Use store_three which has different config
        $this->setCurrentStore('store_three');

        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Store');
        $shippingAddress->setLastname('Three');
        $shippingAddress->setStreet(['Schillerstraße 8']);
        $shippingAddress->setPostcode('60313');
        $shippingAddress->setCity('Frankfurt');
        $shippingAddress->setCountryId('DE');

        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 3);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);
        static::assertEquals('Store Three', $result->getReceiver()->name1);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithStreetParsing()
    {
        $this->setCurrentStore('store_one');

        // Test street parsing functionality
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Street');
        $shippingAddress->setLastname('Parser');
        $shippingAddress->setStreet(['Alexanderplatz 7', 'Apartment 3']);
        $shippingAddress->setPostcode('10178');
        $shippingAddress->setCity('Berlin');
        $shippingAddress->setCountryId('DE');

        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);

        // Verify street was processed
        $receiver = $result->getReceiver();
        static::assertNotEmpty($receiver->streetName);
    }

    /**
     * Test that Packstation addresses trigger the postal facility branch.
     *
     * The observer dhl_versenden/observer::preparePostalFacility() is registered
     * for the dhl_versenden_fetch_postal_facility event. It detects Packstation
     * addresses by checking if street starts with "Packstation" and company
     * contains a numeric post number.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithPackstationAddress()
    {
        $this->setCurrentStore('store_one');

        // Address format that triggers preparePostalFacility observer:
        // - Street must start with "Packstation" followed by 3-digit shop number
        // - Company must be numeric (this becomes the post_number)
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Max');
        $shippingAddress->setLastname('Mustermann');
        $shippingAddress->setStreet(['Packstation 123']);
        $shippingAddress->setCompany('1234567890'); // Valid post number (numeric)
        $shippingAddress->setPostcode('04229');
        $shippingAddress->setCity('Leipzig');
        $shippingAddress->setCountryId('DE');
        $shippingAddress->setEmail('packstation@example.com');

        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);

        // Verify packstation info was populated by the observer
        $packstation = $result->getReceiver()->getPackstation();
        static::assertEquals('123', $packstation->packstationNumber);
        static::assertEquals('1234567890', $packstation->postNumber);
        static::assertEquals('04229', $packstation->zip);
        static::assertEquals('Leipzig', $packstation->city);
    }

    /**
     * Test that Postfiliale addresses trigger the postal facility branch.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithPostfilialeAddress()
    {
        $this->setCurrentStore('store_one');

        // Address format that triggers preparePostalFacility observer for Postfiliale
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Erika');
        $shippingAddress->setLastname('Musterfrau');
        $shippingAddress->setStreet(['Postfiliale 456']);
        $shippingAddress->setCompany('9876543210'); // Valid post number (numeric)
        $shippingAddress->setPostcode('10115');
        $shippingAddress->setCity('Berlin');
        $shippingAddress->setCountryId('DE');
        $shippingAddress->setEmail('postfiliale@example.com');

        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);

        // Verify postfiliale info was populated
        $postfiliale = $result->getReceiver()->getPostfiliale();
        static::assertEquals('456', $postfiliale->postfilialNumber);
        static::assertEquals('9876543210', $postfiliale->postNumber);
        static::assertEquals('10115', $postfiliale->zip);
        static::assertEquals('Berlin', $postfiliale->city);
    }

    /**
     * Test that ParcelShop (Paketshop) addresses trigger the postal facility branch.
     *
     * The module's own observer only handles Packstation and Postfiliale.
     * Paketshop support requires third-party extensions like Dhl_LocationFinder.
     * For testing, we mock the observer to simulate a third-party extension
     * setting the facility data.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function infoFromSalesWithParcelShopAddress()
    {
        $this->setCurrentStore('store_one');

        // Create a mock observer that sets Paketshop facility data
        $observerMock = $this->getModelMock('dhl_versenden/observer', ['preparePostalFacility']);
        $observerMock->expects(static::any())
            ->method('preparePostalFacility')
            ->willReturnCallback(function (Varien_Event_Observer $observer) {
                $facility = $observer->getPostalFacility();
                if ($facility !== null) {
                    $facility->setData([
                        'shop_type' => \Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility::TYPE_PAKETSHOP,
                        'shop_number' => '789',
                    ]);
                }
            });
        $this->replaceByMock('singleton', 'dhl_versenden/observer', $observerMock);

        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setFirstname('Paket');
        $shippingAddress->setLastname('Shop');
        $shippingAddress->setStreet(['Marktstraße 10']);
        $shippingAddress->setPostcode('01067');
        $shippingAddress->setCity('Dresden');
        $shippingAddress->setCountryId('DE');
        $shippingAddress->setEmail('paketshop@example.com');

        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        $builder = new Dhl_Versenden_Model_Info_Builder();
        $result = $builder->infoFromSales($shippingAddress, $serviceInfo, 1);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $result);

        // Verify parcel shop info was populated
        $parcelShop = $result->getReceiver()->getParcelShop();
        static::assertEquals('789', $parcelShop->parcelShopNumber);
        static::assertEquals('01067', $parcelShop->zip);
        static::assertEquals('Dresden', $parcelShop->city);
    }
}
