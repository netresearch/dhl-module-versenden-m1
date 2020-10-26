<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\GlobalSettings;

class Dhl_Versenden_Test_Model_Config_ShipmentTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function getShipmentSettings()
    {
        $config = new Dhl_Versenden_Model_Config_Shipment();

        $settings = $config->getSettings();
        $this->assertInstanceOf(GlobalSettings::class, $settings);
        $this->assertTrue($settings->isPrintOnlyIfCodeable());
        $this->assertEquals('KG', $settings->getUnitOfMeasure());

        $this->assertInternalType('array', $settings->getShippingMethods());
        $this->assertCount(0, $settings->getShippingMethods());

        $this->assertInternalType('array', $settings->getCodPaymentMethods());
        $this->assertCount(1, $settings->getCodPaymentMethods());
        $this->assertContains('cashondelivery', $settings->getCodPaymentMethods());


        $storeSettings = $config->getSettings('store_two');
        $this->assertInstanceOf(GlobalSettings::class, $storeSettings);
        $this->assertFalse($storeSettings->isPrintOnlyIfCodeable());
        $this->assertEquals('G', $storeSettings->getUnitOfMeasure());

        $this->assertInternalType('array', $storeSettings->getShippingMethods());
        $this->assertCount(2, $storeSettings->getShippingMethods());
        $this->assertContains('flatrate_flatrate', $storeSettings->getShippingMethods());
        $this->assertContains('tablerate_bestway', $storeSettings->getShippingMethods());

        $this->assertInternalType('array', $storeSettings->getCodPaymentMethods());
        $this->assertCount(0, $storeSettings->getCodPaymentMethods());
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function canProcessMethod()
    {
        $dhlMethod = 'dhl_ftw';
        $fooMethod = 'foo_bar';

        $settings = new GlobalSettings(false, 'G', array($dhlMethod), array(), 2, 'B64');

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
