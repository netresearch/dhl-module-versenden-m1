<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Model_Resource_Autocreate_CollectionTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Resource_Autocreate_CollectionTest extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @test
     * @loadFixture Model_AutoCreateCollectionTest
     */
    public function addShippingMethodFilter()
    {
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection')->addShippingMethodFilter();
        $this->assertEquals(1, count($collection));
        $this->assertEquals('dhlversenden_foo', $collection->getFirstItem()->getShippingMethod());
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateCollectionTest
     */
    public function addShipmentFilter()
    {
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection')->addShipmentFilter();
        $this->assertEquals(1, count($collection));
        $order = $collection->getFirstItem();
        $this->assertEquals(17, $order->getId());
    }

    /**
     * @test
     * @loadFixture Model_AutoCreateCollectionTest
     */
    public function addDeliveryCountriesFilter()
    {
        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST));
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection')->addDeliveryCountriesFilter($euCountries);
        $this->assertEquals(1, count($collection));
        $order = $collection->getFirstItem();
        $this->assertEquals('DE', $order->getShippingAddress()->getCountryId());
    }


    /**
     * @test
     * @loadFixture Model_AutoCreateCollectionTest
     */
    public function addStatusFilter()
    {
        $statusArray = array('0' => Mage_Sales_Model_Order::STATE_PROCESSING);
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection')->addStatusFilter($statusArray);
        $this->assertEquals(1, count($collection));
        $order = $collection->getFirstItem();
        $this->assertEquals(current($statusArray), $order->getStatus());
    }


    /**
     * @test
     * @loadFixture Model_AutoCreateCollectionTest
     */
    public function addStoreFilter()
    {
        $config = Mage::getModel('dhl_versenden/config');
        $stores = array_filter(
            Mage::app()->getStores(),
            function (Mage_Core_Model_Store $store) use ($config) {
                return $config->isShipmentAutoCreateEnabled($store);
            }
        );
        $usedStore  = current($stores);
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection')->addStoreFilter($stores);
        $this->assertEquals(1, count($collection));
        $order = $collection->getFirstItem();
        $this->assertEquals($usedStore->getId(), $order->getStoreId());
    }

}
