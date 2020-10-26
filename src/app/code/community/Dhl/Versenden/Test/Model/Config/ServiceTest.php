<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Shipment\Service;

class Dhl_Versenden_Test_Model_Config_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getEnabledServices()
    {
        $config = new Dhl_Versenden_Model_Config_Service();

        // (1) parcel announcement is set to required via config fixture
        // → should be in the list of enabled services
        $defaultServiceCollection = $config->getEnabledServices();
        $this->assertInstanceOf(Service\Collection::class, $defaultServiceCollection);
        $defaultServices = $defaultServiceCollection->getItems();
        $this->assertInternalType('array', $defaultServices);

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\ParcelAnnouncement[] $paServices */
        $paServices = array_filter($defaultServices, function ($service) {
            return ($service instanceof Service\ParcelAnnouncement);
        });
        $this->assertNotEmpty($paServices);

        // (2) bulky goods is disabled via config fixture
        // → should not be in the list of enabled services
        $bgServices = array_filter($defaultServices, function ($service) {
            return ($service instanceof Service\BulkyGoods);
        });
        $this->assertNotEmpty($bgServices);

        // (3) parcel announcement is set to optional via config fixture
        // → should be in the list of enabled services
        $storeServiceCollection = $config->getEnabledServices('store_two');
        $this->assertInstanceOf(Service\Collection::class, $storeServiceCollection);
        $storeServices = $storeServiceCollection->getItems();
        $this->assertInternalType('array', $storeServices);

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\ParcelAnnouncement[] $paServices */
        $paServices = array_filter($storeServices, function ($service) {
            return ($service instanceof Service\ParcelAnnouncement);
        });
        $this->assertNotEmpty($paServices);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getPrefDayFee($store = null)
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $this->assertEquals(5, $config->getPrefDayFee());
    }

}
