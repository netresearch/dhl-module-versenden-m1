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
use Dhl\Versenden\Config\Shipment\Settings as ShipmentSettings;
/**
 * Dhl_Versenden_Test_Model_Config_ShipmentTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Config_ShipmentTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture ShipmentTest
     */
    public function getShipmentSettings()
    {
        $config = new Dhl_Versenden_Model_Config();

        $globalSettings = $config->getShipmentSettings();
        $this->assertInstanceOf(ShipmentSettings::class, $globalSettings);
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
        $this->assertInstanceOf(ShipmentSettings::class, $storeSettings);
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

    /**
     * @test
     */
    public function canProcessMethod()
    {
        $dhlMethod = 'dhl_ftw';
        $fooMethod = 'foo_bar';

        $settings = new stdClass();
        $settings->shippingMethods = array($dhlMethod);

        $configMock = $this->getModelMock('dhl_versenden/config', array('getShipmentSettings'));
        $configMock
            ->expects($this->any())
            ->method('getShipmentSettings')
            ->willReturn($settings);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $config = Mage::getModel('dhl_versenden/config');
        $this->assertTrue($config->canProcessMethod($dhlMethod));
        $this->assertFalse($config->canProcessMethod($fooMethod));
    }
}
