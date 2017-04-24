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
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Bcs\Api\Shipment\Service;
/**
 * Dhl_Versenden_Test_Model_Config_ServiceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getPrefTimeFee($store = null)
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $this->assertEquals(7, $config->getPrefTimeFee());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getPrefDayHandlingFeeText($store = null)
    {
        $coreSessionMock = $this
            ->getMockBuilder('Mage_Core_Model_Session')
            ->setMethods(array('start'))
            ->getMock();
        $this->replaceByMock('singleton', 'core/session', $coreSessionMock);

        $config = new Dhl_Versenden_Model_Config_Service();
        $this->assertEquals('This will cost $7.00', $config->getPrefTimeHandlingFeeText());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getPrefTimeHandlingFeeText($store = null)
    {
        $coreSessionMock = $this
            ->getMockBuilder('Mage_Core_Model_Session')
            ->setMethods(array('start'))
            ->getMock();
        $this->replaceByMock('singleton', 'core/session', $coreSessionMock);
        $config = new Dhl_Versenden_Model_Config_Service();
        $this->assertEquals('This will cost $7.00', $config->getPrefTimeHandlingFeeText());
    }
}
