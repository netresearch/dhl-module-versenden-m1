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

/**
 * Dhl_Versenden_Test_Model_ConfigTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_ConfigTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function isAutoloadEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertFalse($config->isAutoloadEnabled());
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function getTitle()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertEquals('foo', $config->getTitle());
        $this->assertEquals('bar', $config->getTitle('store_one'));
        $this->assertEquals('baz', $config->getTitle('store_two'));
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function isActive()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertTrue($config->isActive());
        $this->assertFalse($config->isActive('store_one'));
        $this->assertTrue($config->isActive('store_two'));
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function isSandboxModeEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertTrue($config->isSandboxModeEnabled());
        $this->assertTrue($config->isSandboxModeEnabled('store_one'));
        $this->assertFalse($config->isSandboxModeEnabled('store_two'));
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function isLoggingEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertTrue($config->isLoggingEnabled());
        $this->assertTrue($config->isLoggingEnabled(Zend_Log::DEBUG));
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function getEnabledServices()
    {
        $config = new Dhl_Versenden_Model_Config();

        // (1) parcel announcement is set to required via config fixture
        // → should be in the list of enabled services
        $defaultServiceCollection = $config->getEnabledServices();
        $this->assertInstanceOf(\Dhl\Versenden\Service\Collection::class, $defaultServiceCollection);
        $defaultServices = $defaultServiceCollection->getItems();
        $this->assertInternalType('array', $defaultServices);

        /** @var \Dhl\Versenden\Service\Type\ParcelAnnouncement[] $paServices */
        $paServices = array_filter($defaultServices, function ($service) {
            return ($service instanceof \Dhl\Versenden\Service\Type\ParcelAnnouncement);
        });
        $this->assertNotEmpty($paServices);

        // (2) bulky goods is disabled via config fixture
        // → should not be in the list of enabled services
        $bgServices = array_filter($defaultServices, function ($service) {
            return ($service instanceof \Dhl\Versenden\Service\Type\BulkyGoods);
        });
        $this->assertEmpty($bgServices);

        // (3) parcel announcement is set to optional via config fixture
        // → should be in the list of enabled services
        $storeServiceCollection = $config->getEnabledServices('store_two');
        $this->assertInstanceOf(\Dhl\Versenden\Service\Collection::class, $storeServiceCollection);
        $storeServices = $storeServiceCollection->getItems();
        $this->assertInternalType('array', $storeServices);

        /** @var \Dhl\Versenden\Service\Type\ParcelAnnouncement[] $paServices */
        $paServices = array_filter($storeServices, function ($service) {
            return ($service instanceof \Dhl\Versenden\Service\Type\ParcelAnnouncement);
        });
        $this->assertNotEmpty($paServices);
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function getWebserviceCredentials()
    {
        $config = new Dhl_Versenden_Model_Config();

        $this->assertEquals('uFoo', $config->getWebserviceAuthUsername());
        $this->assertEquals('pFoo', $config->getWebserviceAuthPassword());
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function getWebserviceEndpoint()
    {
        $config = new Dhl_Versenden_Model_Config();

        $this->assertEquals('sandbox endpoint', $config->getEndpoint());
        $this->assertNull($config->getEndpoint('store_two'));
    }
}
