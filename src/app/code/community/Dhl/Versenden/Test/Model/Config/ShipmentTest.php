<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\Data\GlobalSettings;

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
        static::assertInstanceOf(GlobalSettings::class, $settings);
        static::assertTrue($settings->isPrintOnlyIfCodeable());
        static::assertEquals('KG', $settings->getUnitOfMeasure());

        static::assertIsArray($settings->getShippingMethods());
        static::assertCount(0, $settings->getShippingMethods());

        static::assertIsArray($settings->getCodPaymentMethods());
        static::assertCount(1, $settings->getCodPaymentMethods());
        static::assertContains('cashondelivery', $settings->getCodPaymentMethods());


        $storeSettings = $config->getSettings('store_two');
        static::assertInstanceOf(GlobalSettings::class, $storeSettings);
        static::assertFalse($storeSettings->isPrintOnlyIfCodeable());
        static::assertEquals('G', $storeSettings->getUnitOfMeasure());

        static::assertIsArray($storeSettings->getShippingMethods());
        static::assertCount(2, $storeSettings->getShippingMethods());
        static::assertContains('flatrate_flatrate', $storeSettings->getShippingMethods());
        static::assertContains('tablerate_bestway', $storeSettings->getShippingMethods());

        static::assertIsArray($storeSettings->getCodPaymentMethods());
        static::assertCount(0, $storeSettings->getCodPaymentMethods());
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function canProcessMethod()
    {
        $dhlMethod = 'dhl_ftw';
        $fooMethod = 'foo_bar';

        $settings = new GlobalSettings(false, 'G', [$dhlMethod], [], 2, 'B64');

        $configMock = $this->getModelMock('dhl_versenden/config_shipment', ['getSettings']);
        $configMock
            ->expects(static::any())
            ->method('getSettings')
            ->willReturn($settings);
        $this->replaceByMock('model', 'dhl_versenden/config_shipment', $configMock);

        $config = Mage::getModel('dhl_versenden/config_shipment');
        static::assertTrue($config->canProcessMethod($dhlMethod));
        static::assertFalse($config->canProcessMethod($fooMethod));
    }
}
