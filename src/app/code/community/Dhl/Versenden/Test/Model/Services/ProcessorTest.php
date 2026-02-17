<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

/**
 * Test Services Processor functionality.
 *
 * This processor handles API-based service enrichment and filtering
 * based on product availability and backorder status.
 */
class Dhl_Versenden_Test_Model_Services_ProcessorTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Service\Collection
     */
    protected $serviceCollection;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a basic service collection for testing
        $this->serviceCollection = new Service\Collection([
            new Service\PreferredDay('Preferred Day', true, false, []),
            new Service\PreferredLocation('Preferred Location', true, false, ''),
            new Service\PreferredNeighbour('Preferred Neighbour', true, false, ''),
            new Service\ParcelAnnouncement('Parcel Announcement', true, false),
        ]);
    }

    /**
     * Create a mock shipping address with the given country code.
     *
     * @param string $countryId
     * @return PHPUnit\Framework\MockObject\MockObject
     */
    protected function createAddressMock($countryId)
    {
        $addressMock = $this->getMockBuilder(Mage_Sales_Model_Order_Address::class)
            ->setMethods(['getCountryId'])
            ->getMock();
        $addressMock->method('getCountryId')->willReturn($countryId);

        return $addressMock;
    }

    /**
     * Create a mock quote/order with a shipping address and item list.
     *
     * @param string $countryId Recipient country code
     * @param array $items Quote items (empty array for no items)
     * @return PHPUnit\Framework\MockObject\MockObject
     */
    protected function createQuoteMock($countryId, array $items = [])
    {
        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->setMethods(['getShippingAddress', 'getAllItems'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($this->createAddressMock($countryId));
        $quoteMock->method('getAllItems')->willReturn($items);

        return $quoteMock;
    }

    /**
     * Test that processServices returns unchanged collection when API call succeeds
     * and no backordered products exist.
     *
     * @test
     */
    public function processServicesReturnsCollectionOnApiSuccess()
    {
        $quoteMock = $this->createQuoteMock('DE');

        // Mock API service that returns available services
        $apiServiceMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getAvailable'])
            ->getMock();
        $apiServiceMock->method('getAvailable')->willReturn(true);

        // Mock checkout service
        $checkoutServiceMock = $this->getModelMock(
            'dhl_versenden/services_checkoutService',
            ['getRecipientZipAvailableServices', 'getService'],
        );
        $checkoutServiceMock->method('getRecipientZipAvailableServices')->willReturn(null);
        $checkoutServiceMock->method('getService')->willReturn($apiServiceMock);
        $this->replaceByMock('singleton', 'dhl_versenden/services_checkoutService', $checkoutServiceMock);

        // Mock service options
        $serviceOptionsMock = $this->getModelMock('dhl_versenden/services_serviceOptions', ['getOptions']);
        $serviceOptionsMock->method('getOptions')->willReturn([]);
        $this->replaceByMock('model', 'dhl_versenden/services_serviceOptions', $serviceOptionsMock);

        $processor = Mage::getModel('dhl_versenden/services_processor', ['quote' => $quoteMock]);
        $result = $processor->processServices($this->serviceCollection);

        static::assertInstanceOf(Service\Collection::class, $result);
        // PreferredLocation and PreferredNeighbour should remain (not in API mapping)
        static::assertNotNull($result->getItem(Service\PreferredLocation::CODE));
        static::assertNotNull($result->getItem(Service\PreferredNeighbour::CODE));
    }

    /**
     * Test that processServices removes online-only services when API call fails.
     *
     * @test
     */
    public function processServicesRemovesOnlineServicesOnApiFailure()
    {
        $quoteMock = $this->createQuoteMock('DE');

        // Mock checkout service that throws exception
        $checkoutServiceMock = $this->getModelMock(
            'dhl_versenden/services_checkoutService',
            ['getRecipientZipAvailableServices'],
        );
        $checkoutServiceMock->method('getRecipientZipAvailableServices')
            ->willThrowException(new Exception('API Error'));
        $this->replaceByMock('singleton', 'dhl_versenden/services_checkoutService', $checkoutServiceMock);

        $processor = Mage::getModel('dhl_versenden/services_processor', ['quote' => $quoteMock]);
        $result = $processor->processServices($this->serviceCollection);

        // PreferredDay should be removed (online-only)
        static::assertNull($result->getItem(Service\PreferredDay::CODE));

        // Other services should remain
        static::assertNotNull($result->getItem(Service\PreferredLocation::CODE));
        static::assertNotNull($result->getItem(Service\PreferredNeighbour::CODE));
        static::assertNotNull($result->getItem(Service\ParcelAnnouncement::CODE));
    }

    /**
     * Test that processServices removes PreferredDay when backorder products exist.
     *
     * @test
     */
    public function processServicesRemovesPreferredDayForBackorders()
    {
        // Create stock item with zero quantity
        $stockItemMock = $this->getMockBuilder(Mage_CatalogInventory_Model_Stock_Item::class)
            ->setMethods(['getQty'])
            ->getMock();
        $stockItemMock->method('getQty')->willReturn(0);

        // Create product with stock item
        $productMock = $this->getMockBuilder(Mage_Catalog_Model_Product::class)
            ->setMethods(['getStockItem'])
            ->getMock();
        $productMock->method('getStockItem')->willReturn($stockItemMock);

        // Create quote item
        $itemMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Item::class)
            ->setMethods(['getProduct', 'getParentItemId', 'getQty', 'getChildren'])
            ->getMock();
        $itemMock->method('getProduct')->willReturn($productMock);
        $itemMock->method('getParentItemId')->willReturn(null);
        $itemMock->method('getQty')->willReturn(1);
        $itemMock->method('getChildren')->willReturn([]);

        $quoteMock = $this->createQuoteMock('DE', [$itemMock]);

        // Mock API service that returns available
        $apiServiceMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getAvailable'])
            ->getMock();
        $apiServiceMock->method('getAvailable')->willReturn(true);

        // Mock checkout service
        $checkoutServiceMock = $this->getModelMock(
            'dhl_versenden/services_checkoutService',
            ['getRecipientZipAvailableServices', 'getService'],
        );
        $checkoutServiceMock->method('getRecipientZipAvailableServices')->willReturn(null);
        $checkoutServiceMock->method('getService')->willReturn($apiServiceMock);
        $this->replaceByMock('singleton', 'dhl_versenden/services_checkoutService', $checkoutServiceMock);

        // Mock service options
        $serviceOptionsMock = $this->getModelMock('dhl_versenden/services_serviceOptions', ['getOptions']);
        $serviceOptionsMock->method('getOptions')->willReturn([]);
        $this->replaceByMock('model', 'dhl_versenden/services_serviceOptions', $serviceOptionsMock);

        $processor = Mage::getModel('dhl_versenden/services_processor', ['quote' => $quoteMock]);
        $result = $processor->processServices($this->serviceCollection);

        // PreferredDay should be removed due to backorder
        static::assertNull($result->getItem(Service\PreferredDay::CODE));

        // Other services should remain
        static::assertNotNull($result->getItem(Service\PreferredLocation::CODE));
    }

    /**
     * Test that processServices skips CIG API call for non-DE recipients.
     *
     * The CIG Checkout API only supports German postcodes. For international
     * recipients, the API should not be called at all — online-only services
     * (PreferredDay) should be removed, and other services preserved.
     *
     * @test
     */
    public function processServicesSkipsCigApiForNonDeRecipient()
    {
        $quoteMock = $this->createQuoteMock('AT');

        // CIG API must NOT be called for non-DE recipient
        $checkoutServiceMock = $this->getModelMock(
            'dhl_versenden/services_checkoutService',
            ['getRecipientZipAvailableServices'],
        );
        $checkoutServiceMock->expects($this->never())
            ->method('getRecipientZipAvailableServices');
        $this->replaceByMock('singleton', 'dhl_versenden/services_checkoutService', $checkoutServiceMock);

        $processor = Mage::getModel('dhl_versenden/services_processor', ['quote' => $quoteMock]);
        $result = $processor->processServices($this->serviceCollection);

        // PreferredDay should be removed (online-only, no API data)
        static::assertNull($result->getItem(Service\PreferredDay::CODE));

        // Non-CIG services should be preserved
        static::assertNotNull($result->getItem(Service\ParcelAnnouncement::CODE));
    }

    /**
     * Test that processServices still calls CIG API for DE recipients.
     *
     * @test
     */
    public function processServicesCallsCigApiForDeRecipient()
    {
        $quoteMock = $this->createQuoteMock('DE');

        // Mock API service
        $apiServiceMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getAvailable'])
            ->getMock();
        $apiServiceMock->method('getAvailable')->willReturn(true);

        // CIG API MUST be called for DE recipient
        $checkoutServiceMock = $this->getModelMock(
            'dhl_versenden/services_checkoutService',
            ['getRecipientZipAvailableServices', 'getService'],
        );
        $checkoutServiceMock->expects($this->once())
            ->method('getRecipientZipAvailableServices');
        $checkoutServiceMock->method('getService')->willReturn($apiServiceMock);
        $this->replaceByMock('singleton', 'dhl_versenden/services_checkoutService', $checkoutServiceMock);

        // Mock service options
        $serviceOptionsMock = $this->getModelMock('dhl_versenden/services_serviceOptions', ['getOptions']);
        $serviceOptionsMock->method('getOptions')->willReturn([]);
        $this->replaceByMock('model', 'dhl_versenden/services_serviceOptions', $serviceOptionsMock);

        $processor = Mage::getModel('dhl_versenden/services_processor', ['quote' => $quoteMock]);
        $result = $processor->processServices($this->serviceCollection);

        // ParcelAnnouncement should still be present
        static::assertNotNull($result->getItem(Service\ParcelAnnouncement::CODE));
    }

    /**
     * Test that processServices keeps PreferredDay when no backorders exist.
     *
     * @test
     */
    public function processServicesKeepsPreferredDayWithoutBackorders()
    {
        // Create stock item with sufficient quantity
        $stockItemMock = $this->getMockBuilder(Mage_CatalogInventory_Model_Stock_Item::class)
            ->setMethods(['getQty'])
            ->getMock();
        $stockItemMock->method('getQty')->willReturn(100);

        // Create product with stock item
        $productMock = $this->getMockBuilder(Mage_Catalog_Model_Product::class)
            ->setMethods(['getStockItem'])
            ->getMock();
        $productMock->method('getStockItem')->willReturn($stockItemMock);

        // Create quote item
        $itemMock = $this->getMockBuilder(Mage_Sales_Model_Quote_Item::class)
            ->setMethods(['getProduct', 'getParentItemId', 'getQty', 'getChildren'])
            ->getMock();
        $itemMock->method('getProduct')->willReturn($productMock);
        $itemMock->method('getParentItemId')->willReturn(null);
        $itemMock->method('getQty')->willReturn(1);
        $itemMock->method('getChildren')->willReturn([]);

        $quoteMock = $this->createQuoteMock('DE', [$itemMock]);

        // Mock API service that returns available
        $apiServiceMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getAvailable'])
            ->getMock();
        $apiServiceMock->method('getAvailable')->willReturn(true);

        // Mock checkout service
        $checkoutServiceMock = $this->getModelMock(
            'dhl_versenden/services_checkoutService',
            ['getRecipientZipAvailableServices', 'getService'],
        );
        $checkoutServiceMock->method('getRecipientZipAvailableServices')->willReturn(null);
        $checkoutServiceMock->method('getService')->willReturn($apiServiceMock);
        $this->replaceByMock('singleton', 'dhl_versenden/services_checkoutService', $checkoutServiceMock);

        // Mock service options
        $serviceOptionsMock = $this->getModelMock('dhl_versenden/services_serviceOptions', ['getOptions']);
        $serviceOptionsMock->method('getOptions')->willReturn([]);
        $this->replaceByMock('model', 'dhl_versenden/services_serviceOptions', $serviceOptionsMock);

        $processor = Mage::getModel('dhl_versenden/services_processor', ['quote' => $quoteMock]);
        $result = $processor->processServices($this->serviceCollection);

        // PreferredDay should remain since there's enough stock
        static::assertNotNull($result->getItem(Service\PreferredDay::CODE));
    }
}
