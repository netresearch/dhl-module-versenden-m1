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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\GlobalSettings;
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
        $config = new Dhl_Versenden_Model_Config_Shipment();

        $settings = $config->getSettings();
        $this->assertInstanceOf(GlobalSettings::class, $settings);
        $this->assertTrue($settings->isPrintOnlyIfCodable());
        $this->assertEquals('G', $settings->getUnitOfMeasure());
        $this->assertEquals(200, $settings->getProductWeight());
        $this->assertEquals(2, $settings->getCodCharge());

        $this->assertInternalType('array', $settings->getShippingMethods());
        $this->assertCount(0, $settings->getShippingMethods());

        $this->assertInternalType('array', $settings->getCodPaymentMethods());
        $this->assertCount(1, $settings->getCodPaymentMethods());
        $this->assertContains('cashondelivery', $settings->getCodPaymentMethods());


        $storeSettings = $config->getSettings('store_two');
        $this->assertInstanceOf(GlobalSettings::class, $storeSettings);
        $this->assertFalse($storeSettings->isPrintOnlyIfCodable());
        $this->assertEquals('KG', $storeSettings->getUnitOfMeasure());
        $this->assertEquals(0.2, $storeSettings->getProductWeight());
        $this->assertEquals(11, $storeSettings->getCodCharge());

        $this->assertInternalType('array', $storeSettings->getShippingMethods());
        $this->assertCount(2, $storeSettings->getShippingMethods());
        $this->assertContains('flatrate_flatrate', $storeSettings->getShippingMethods());
        $this->assertContains('tablerate_bestway', $storeSettings->getShippingMethods());

        $this->assertInternalType('array', $storeSettings->getCodPaymentMethods());
        $this->assertCount(0, $storeSettings->getCodPaymentMethods());
    }

    /**
     * @test
     */
    public function canProcessMethod()
    {
        $dhlMethod = 'dhl_ftw';
        $fooMethod = 'foo_bar';

        $settings = new GlobalSettings(false, 'G', 0.25, array($dhlMethod), array(), 2, 'B64');

        $configMock = $this->getModelMock('dhl_versenden/config_shipment', array('getSettings'));
        $configMock
            ->expects($this->any())
            ->method('getSettings')
            ->willReturn($settings);
        $this->replaceByMock('model', 'dhl_versenden/config_shipment', $configMock);

        $config = Mage::getModel('dhl_versenden/config_shipment');
        $this->assertTrue($config->canProcessMethod($dhlMethod));
        $this->assertFalse($config->canProcessMethod($fooMethod));
    }
}
