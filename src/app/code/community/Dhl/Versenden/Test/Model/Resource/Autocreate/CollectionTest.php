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

        $this->assertNotEmpty($collection);

        /** @var Mage_Sales_Model_Order $order */
        foreach ($collection as $order) {
            $this->assertStringStartsWith('dhlversenden_', $order->getShippingMethod());
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

        $this->assertContains('10', $orderIds);
        $this->assertNotContains('11', $orderIds);
        $this->assertContains('17', $orderIds);
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
        $this->assertContains('10', $orderIds);
        $this->assertNotContains('11', $orderIds);
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateTest
     */
    public function addStatusFilter()
    {
        $statusArray = array('pending');

        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStatusFilter($statusArray);

        $orderIds = $collection->getAllIds();
        $this->assertContains('10', $orderIds);
        $this->assertNotContains('11', $orderIds);


        $statusArray = array('pending', 'processing');

        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStatusFilter($statusArray);

        $orderIds = $collection->getAllIds();
        $this->assertContains('10', $orderIds);
        $this->assertContains('11', $orderIds);
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateTest
     */
    public function addStoreFilter()
    {
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStoreFilter(array('store_one'));

        $orderIds = $collection->getAllIds();
        $this->assertContains('10', $orderIds);
        $this->assertContains('11', $orderIds);
        $this->assertNotContains('17', $orderIds);


        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addStoreFilter(array('store_one', 'store_two'));

        $orderIds = $collection->getAllIds();
        $this->assertContains('10', $orderIds);
        $this->assertContains('11', $orderIds);
        $this->assertContains('17', $orderIds);
    }
}
