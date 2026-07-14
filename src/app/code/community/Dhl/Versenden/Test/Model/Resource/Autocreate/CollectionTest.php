<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Resource_Autocreate_CollectionTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_AutoCreateTest
     */
    public function addShippingMethodFilter()
    {
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShippingMethodFilter();

        static::assertNotEmpty($collection);

        /** @var Mage_Sales_Model_Order $order */
        foreach ($collection as $order) {
            static::assertStringStartsWith('dhlversenden_', $order->getShippingMethod());
        }
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateTest
     */
    public function addShipmentFilter()
    {
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addShipmentFilter();

        $orderIds = $collection->getAllIds();

        static::assertContains('10', $orderIds);
        static::assertNotContains('11', $orderIds);
        static::assertContains('17', $orderIds);
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateTest
     */
    public function addDeliveryCountriesFilter()
    {
        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST));

        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection = $collection->addDeliveryCountriesFilter($euCountries);

        $orderIds = $collection->getAllIds();
        static::assertContains('10', $orderIds);
        static::assertNotContains('11', $orderIds);
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateTest
     */
    public function addStatusFilter()
    {
        $statusArray = ['pending'];

        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStatusFilter($statusArray);

        $orderIds = $collection->getAllIds();
        static::assertContains('10', $orderIds);
        static::assertNotContains('11', $orderIds);


        $statusArray = ['pending', 'processing'];

        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStatusFilter($statusArray);

        $orderIds = $collection->getAllIds();
        static::assertContains('10', $orderIds);
        static::assertContains('11', $orderIds);
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateTest
     */
    public function addStoreFilter()
    {
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStoreFilter(['store_one']);

        $orderIds = $collection->getAllIds();
        static::assertContains('10', $orderIds);
        static::assertContains('11', $orderIds);
        static::assertNotContains('17', $orderIds);


        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStoreFilter(['store_one', 'store_two']);

        $orderIds = $collection->getAllIds();
        static::assertContains('10', $orderIds);
        static::assertContains('11', $orderIds);
        static::assertContains('17', $orderIds);
    }

    /**
     * Create a sales order with the given destination country.
     *
     * @param int $entityId
     * @param string $incrementId
     * @param string|null $countryId
     * @return Mage_Sales_Model_Order
     */
    protected function createOrderWithDestination($entityId, $incrementId, $countryId)
    {
        $order = Mage::getModel('sales/order');
        $order->setEntityId($entityId);
        $order->setIncrementId($incrementId);

        if ($countryId !== null) {
            $address = Mage::getModel('sales/order_address');
            $address->setCountryId($countryId);
            $order->setShippingAddress($address);
        }

        return $order;
    }

    /**
     * Assert that non-EU destinations are removed from the collection and returned.
     *
     * @test
     */
    public function removeNonDeliveryCountryItems()
    {
        $euCountries = ['DE', 'AT', 'FR'];

        /** @var Dhl_Versenden_Model_Resource_Autocreate_Collection $collection */
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addItem($this->createOrderWithDestination(1, '100000001', 'DE'));
        $collection->addItem($this->createOrderWithDestination(2, '100000002', 'US'));
        $collection->addItem($this->createOrderWithDestination(3, '100000003', 'AT'));
        $collection->addItem($this->createOrderWithDestination(4, '100000004', 'CH'));
        $collection->addItem($this->createOrderWithDestination(5, '100000005', null));

        $removed = $collection->removeNonDeliveryCountryItems($euCountries);

        $removedIncrements = array_map(
            function (Mage_Sales_Model_Order $order) {
                return $order->getIncrementId();
            },
            $removed,
        );
        static::assertSame(['100000002', '100000004', '100000005'], array_values($removedIncrements));

        $remainingIds = array_keys($collection->getItems());
        static::assertSame([1, 3], array_map('intval', $remainingIds));
    }
}
