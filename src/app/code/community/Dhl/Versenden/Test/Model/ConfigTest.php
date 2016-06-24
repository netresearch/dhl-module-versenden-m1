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
use Dhl\Versenden\Config;
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
    public function getShipperAccount()
    {
        $config = new Dhl_Versenden_Model_Config();

        $testAccount = $config->getShipperAccount();
        $this->assertInstanceOf(Config\Shipper\Account::class, $testAccount);
        $this->assertEquals('2222222222_01', $testAccount->user);
        $this->assertEquals('pass', $testAccount->signature);
        $this->assertEquals('2222222222', $testAccount->ekp);
        $this->assertTrue($testAccount->goGreen);
        $this->assertEquals('01', $testAccount->participation->dhlPaket);
        $this->assertEquals('01', $testAccount->participation->dhlReturnShipment);

        $prodAccount = $config->getShipperAccount('store_two');
        $this->assertInstanceOf(Config\Shipper\Account::class, $prodAccount);
        $this->assertEquals('303', $prodAccount->user);
        $this->assertEquals('magento', $prodAccount->signature);
        $this->assertEquals('909', $prodAccount->ekp);
        $this->assertFalse($prodAccount->goGreen);
        $this->assertEquals('98', $prodAccount->participation->dhlPaket);
        $this->assertEquals('99', $prodAccount->participation->dhlReturnShipment);
    }

    /**
     * @test
     * @loadFixture ConfigTest
     */
    public function getShipmentSettings()
    {
        $config = new Dhl_Versenden_Model_Config();

        $globalSettings = $config->getShipmentSettings();
        $this->assertInstanceOf(Config\Shipment\Settings::class, $globalSettings);
        $this->assertTrue($globalSettings->printOnlyIfCodable);
        $this->assertEquals('G', $globalSettings->unitOfMeasure);
        $this->assertEquals(200, $globalSettings->productWeight);
        $this->assertEquals(2, $globalSettings->codCharge);

        $this->assertInternalType('array', $globalSettings->shippingMethods);
        $this->assertCount(0, $globalSettings->shippingMethods);

        $this->assertInternalType('array', $globalSettings->codPaymentMethods);
        $this->assertCount(1, $globalSettings->codPaymentMethods);
        $this->assertContains('cashondelivery', $globalSettings->codPaymentMethods);


        $storeSettings = $config->getShipmentSettings('store_two');
        $this->assertInstanceOf(Config\Shipment\Settings::class, $storeSettings);
        $this->assertFalse($storeSettings->printOnlyIfCodable);
        $this->assertEquals('KG', $storeSettings->unitOfMeasure);
        $this->assertEquals(0.2, $storeSettings->productWeight);
        $this->assertEquals(11, $storeSettings->codCharge);

        $this->assertInternalType('array', $storeSettings->shippingMethods);
        $this->assertCount(2, $storeSettings->shippingMethods);
        $this->assertContains('flatrate_flatrate', $storeSettings->shippingMethods);
        $this->assertContains('tablerate_bestway', $storeSettings->shippingMethods);

        $this->assertInternalType('array', $storeSettings->codPaymentMethods);
        $this->assertCount(0, $storeSettings->codPaymentMethods);
    }
}
